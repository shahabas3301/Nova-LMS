<?php

namespace Modules\LaraPayease\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LaraPayease\Facades\PaymentDriver;

class MonerooPaymentController extends Controller
{
    public function prepareCharge(Request $request)
    {
        try {
            $response = PaymentDriver::moneroo()->createSession($request->all());

            if (($response['status'] ?? 400) !== 200) {
                return response()->json([
                    'msg' => $response['message'] ?? __('Unable to create Moneroo payment'),
                ], 400);
            }

            return response()->json([
                'checkout_url'   => $response['checkout_url'] ?? null,
                'transaction_id' => $response['transaction_id'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'msg' => $e->getMessage(),
            ], 400);
        }
    }
}
