<?php

namespace Modules\LaraPayease\Drivers;

use Illuminate\Support\Facades\Log;
use Moneroo\Payment as MonerooPayment;
use Modules\LaraPayease\Traits\Currency;
use Modules\LaraPayease\BasePaymentDriver;
use Modules\LaraPayease\Facades\PaymentDriver;
use Symfony\Component\HttpFoundation\Response;

class Moneroo extends BasePaymentDriver
{
    use Currency;

    public function driverName(): string
    {
        return 'moneroo';
    }

    public function chargeCustomer(array $params)
    {
        // Ensure keys/currency are initialized once
        if (!isset($this->keys)) {
            $gateways = PaymentDriver::supportedGateways();
            $config   = $gateways['moneroo'] ?? [];

            if (!empty($config['keys'])) {
                $this->setKeys($config['keys']);
            }

            if (!empty($config['currency'])) {
                $this->setCurrency($config['currency']);
            } else {
                $this->setCurrency('EUR'); // default France
            }

            if (array_key_exists('exchange_rate', $config)) {
                $this->setExchangeRate($config['exchange_rate']);
            }
        }

        $keys = $this->getKeys();

        if (empty($keys['secret_key'])) {
            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => __('Missing Moneroo secret key'),
            ];
        }

        return view('larapayease::moneroo', [
            'moneroo_data' => array_merge($params, [
                'moneroo_secret' => base64_encode($keys['secret_key']),
                'currency'       => $this->getCurrency() ?: 'EUR',
                'charge_amount'  => (int) $this->chargeableAmount($params['amount']),
            ]),
        ]);
    }

    /**
     * Called via AJAX from the moneroo.blade.js (like StripePaymentController::prepareCharge).
     * This is where we actually hit the Moneroo API.
     */
    public function createSession(array $params): array
    {
        // Prefer secret coming from the form (encoded in the view)
        $secretKey = !empty($params['moneroo_secret'])
            ? base64_decode($params['moneroo_secret'])
            : null;

        if (!$secretKey) {
            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => __('Missing Moneroo secret key'),
            ];
        }

        // Split name
        $fullName  = $params['name'] ?? '';
        $nameParts = preg_split('/\s+/', trim($fullName), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName  = $nameParts[1] ?? $nameParts[0] ?? '';

        // Amount & currency
        $amount   = (int) ($params['charge_amount'] ?? $this->chargeableAmount($params['amount']));
        $currency = $params['currency'] ?? 'EUR';

        $paymentData = [
            'amount'      => $amount,
            'currency'    => $currency,
            'description' => $params['description'] ?? $params['title'] ?? '',
            'return_url'  => $params['ipn_url'],

            'customer' => [
                'email'      => $params['email'] ?? null,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'phone'      => $params['phone'] ?? null,
            ],

            'metadata' => [
                'order_id'     => $params['order_id'] ?? null,
                'payment_type' => $params['payment_type'] ?? null,
            ],
        ];

        try {
            $moneroo  = new MonerooPayment($secretKey);
            $payment  = $moneroo->init($paymentData);

            return [
                'status'         => Response::HTTP_OK,
                'checkout_url'   => $payment->checkout_url ?? null,
                'transaction_id' => $payment->id ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Moneroo createSession error: ' . $e->getMessage(), [
                'payload' => $paymentData,
            ]);

            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify the payment after Moneroo redirects or via webhook.
     * You should pass in the transaction id you get from:
     *  - query string in return_url (paymentId)
     *  - or stored when chargeCustomer() is called
     */
    public function paymentResponse(array $params = [])
    {
        // Initialize keys first
        $keys = $this->getKeys();

        $transactionId = $params['transaction_id']
            ?? $params['paymentId']
            ?? $params['id']
            ?? null;

        if (!$transactionId) {
            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => __('Missing Moneroo transaction id'),
            ];
        }

        try {
            $moneroo = new MonerooPayment($keys['secret_key']);
            $payment = $moneroo->verify($transactionId);

            // SDK returns a response object with `status` inside `data`
            // In the raw API response, status lives at data.status
            $data   = $payment->data ?? $payment;
            $status = $data->status ?? $data['status'] ?? null;

            $meta = $data->metadata ?? $data['metadata'] ?? [];

            if ($status === 'success') {
                return [
                    'status' => Response::HTTP_OK,
                    'data'   => [
                        'transaction_id' => $data->id ?? $transactionId,
                        'order_id'       => $meta->order_id ?? ($meta['order_id'] ?? null),
                    ],
                ];
            }

            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => __('Payment not successful'),
                'order_id' => $meta->order_id ?? ($meta['order_id'] ?? null),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ];
        }
    }
}
