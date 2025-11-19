<?php

use Illuminate\Support\Facades\Route;
use Modules\LaraPayease\Http\Controllers\StripePaymentController;
use Modules\LaraPayease\Http\Controllers\MonerooPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('payease/stripe', [StripePaymentController::class, 'prepareCharge'])->name('payease.stripe');
Route::post('payment/moneroo', [MonerooPaymentController::class, 'prepareCharge'])->name('payease.moneroo');
