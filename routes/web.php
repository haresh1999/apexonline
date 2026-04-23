<?php

use App\Http\Controllers\{
    CcavenueController,
    CcavenueSandboxController,
    HdfcController,
    HdfcSandboxController,
    PhonepeController,
    PhonepeSandboxController,
    RazorpayController,
    RazorpaySandboxController,
    SabpaisaController,
    SabpaisaSandboxController,
    PaytmController,
    PaytmSandboxController,
    PayuController,
    PayuSandboxController,
    TransactionController,
    ZohoController,
    ZohoSandboxController,
};

use Illuminate\Support\Facades\Route;

Route::prefix('{env?}')->group(function () {
    Route::get('token', [TransactionController::class, 'getToken'])->middleware('token');
    Route::post('request', [TransactionController::class, 'request'])->middleware(['throttle:600,10', 'auth', 'signature']);
    Route::post('status', [TransactionController::class, 'status'])->middleware(['throttle:600,10', 'auth']);
    Route::get('redirect', [TransactionController::class, 'redirect'])->middleware(['throttle:60,1']);
    Route::get('payment/verify', [TransactionController::class, 'verifyPayment']);
    Route::post('payment/update', [TransactionController::class, 'paymentUpdate']);
    Route::get('generate-sign', [TransactionController::class, 'signatureGenerate']);
})->where('env', 'sandbox');

// RAZORPAY
// Route::prefix('razorpay')->group(function () {
//     Route::post('get/order-id', [RazorpayController::class, 'getOrderId']);
//     Route::get('checkout/order-pay/{id}', [RazorpayController::class, 'orderPay']);
//     Route::post('callback', [RazorpayController::class, 'callback'])->middleware(['throttle:60,1']);
// });

// Route::prefix('razorpay/sandbox')->group(function () {
//     Route::post('get/order-id', [RazorpaySandboxController::class, 'getOrderId']);
//     Route::get('checkout/order-pay/{id}', [RazorpaySandboxController::class, 'orderPay']);
//     Route::post('callback', [RazorpaySandboxController::class, 'callback'])->middleware(['throttle:60,1']);
// });

// SABPAISA
// Route::prefix('sabpaisa')->group(function () {
//     Route::post('request', [SabpaisaController::class, 'request'])->middleware('sabpaisa');
//     Route::any('callback', [SabpaisaController::class, 'callback']);

//     Route::prefix('sandbox')->group(function () {
//         Route::post('request', [SabpaisaSandboxController::class, 'request'])->middleware('sabpaisa');
//         Route::any('callback', [SabpaisaSandboxController::class, 'callback']);
//     });
// });

// PHONEPE
// Route::prefix('phonepe')->group(function () {
//     Route::post('create', [PhonepeController::class, 'create'])->middleware('phonepe');
//     Route::post('request', [PhonepeController::class, 'request']);
//     Route::any('callback', [PhonepeController::class, 'callback']);

//     Route::prefix('sandbox')->group(function () {
//         Route::post('create', [PhonepeSandboxController::class, 'create'])->middleware('phonepe');
//         Route::post('request', [PhonepeSandboxController::class, 'request']);
//         Route::any('callback', [PhonepeSandboxController::class, 'callback']);
//     });
// });

// PAYTM
// Route::prefix('paytm')->group(function () {
//     Route::post('create', [PaytmController::class, 'create'])->middleware('paytm');
//     Route::post('request', [PaytmController::class, 'request'])->middleware('paytm');
//     Route::any('callback', [PaytmController::class, 'callback']);

//     Route::prefix('sandbox')->group(function () {
//         Route::post('create', [PaytmSandboxController::class, 'create'])->middleware('paytm');
//         Route::post('request', [PaytmSandboxController::class, 'request'])->middleware('paytm');
//         Route::any('callback', [PaytmSandboxController::class, 'callback']);
//     });
// });

// CCAVENUE
Route::prefix('ccavenue')->group(function () {
    Route::get('request', [CcavenueController::class, 'request']);
    Route::any('callback', [CcavenueController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [CcavenueSandboxController::class, 'request']);
        Route::any('callback', [CcavenueSandboxController::class, 'callback']);
    });
});

// HDFC
Route::prefix('hdfc')->group(function () {
    Route::get('request', [HdfcController::class, 'request']);
    Route::any('callback', [HdfcController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [HdfcSandboxController::class, 'request']);
        Route::any('callback', [HdfcSandboxController::class, 'callback']);
    });
});

// ZOHO
Route::prefix('zoho')->group(function () {
    Route::get('request', [ZohoController::class, 'request']);
    Route::any('callback', [ZohoController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [ZohoSandboxController::class, 'request']);
        Route::any('callback', [ZohoSandboxController::class, 'callback']);
    });
});

Route::prefix('payu')->group(function () {
    Route::get('request', [PayuController::class, 'request']);
    Route::any('callback', [PayuController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [PayuSandboxController::class, 'request']);
        Route::any('callback', [PayuSandboxController::class, 'callback']);
    });
});

Route::view('test-payment', 'request_payment');
