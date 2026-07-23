<?php

use App\Http\Controllers\{
    CashfreeController,
    CashfreeSandboxController,
    CcavenueController,
    CcavenueSandboxController,
    HdfcController,
    HdfcSandboxController,
    InstaMojoController,
    InstaMojoSandboxController,
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

use App\Http\Controllers\Admin\{
    LoginController,
    CommonController,
    CompanyController,
    DashboardController,
    GatewayController,
    TransactionController as SalesController,
    UserController,
};
use App\Http\Middleware\Admin\PermissionMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('sandbox')->group(function () {
    Route::post('token', [TransactionController::class, 'getToken'])->middleware('token');
    Route::post('request', [TransactionController::class, 'request'])->middleware(['throttle:600,10', 'auth', 'signature']);
    Route::post('status', [TransactionController::class, 'status'])->middleware(['throttle:600,10', 'auth']);
    Route::get('redirect', [TransactionController::class, 'redirect'])->middleware(['throttle:60,1']);
    Route::get('generate-sign', [TransactionController::class, 'signatureGenerate']);
});

Route::prefix('/')->group(function () {
    Route::post('token', [TransactionController::class, 'getToken'])->middleware('token');
    Route::post('request', [TransactionController::class, 'request'])->middleware(['throttle:600,10', 'auth', 'signature']);
    Route::post('status', [TransactionController::class, 'status'])->middleware(['throttle:600,10', 'auth']);
    Route::get('redirect', [TransactionController::class, 'redirect'])->middleware(['throttle:60,1']);
    Route::get('generate-sign', [TransactionController::class, 'signatureGenerate']);
});

// RAZORPAY
Route::prefix('razorpay')->group(function () {
    Route::post('get/order-id', [RazorpayController::class, 'getOrderId']);
    Route::get('checkout/order-pay/{id}', [RazorpayController::class, 'orderPay']);
    Route::post('callback', [RazorpayController::class, 'callback'])->middleware(['throttle:60,1']);
});

Route::prefix('razorpay/sandbox')->group(function () {
    Route::post('get/order-id', [RazorpaySandboxController::class, 'getOrderId']);
    Route::get('checkout/order-pay/{id}', [RazorpaySandboxController::class, 'orderPay']);
    Route::post('callback', [RazorpaySandboxController::class, 'callback'])->middleware(['throttle:60,1']);
});

// SABPAISA
Route::prefix('sabpaisa')->group(function () {
    Route::get('request', [SabpaisaController::class, 'request']);
    Route::any('callback', [SabpaisaController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [SabpaisaSandboxController::class, 'request']);
        Route::any('callback', [SabpaisaSandboxController::class, 'callback']);
    });
});

// PHONEPE
Route::prefix('phonepe')->group(function () {
    Route::get('request', [PhonepeController::class, 'request']);
    Route::post('redirect', [PhonepeController::class, 'redirect']);
    Route::any('callback', [PhonepeController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [PhonepeSandboxController::class, 'request']);
        Route::post('redirect', [PhonepeSandboxController::class, 'redirect']);
        Route::any('callback', [PhonepeSandboxController::class, 'callback']);
    });
});

// PAYTM
Route::prefix('paytm')->group(function () {
    Route::get('request', [PaytmController::class, 'request']);
    Route::get('create', [PaytmController::class, 'create']);
    Route::any('callback', [PaytmController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [PaytmSandboxController::class, 'request']);
        Route::get('create', [PaytmSandboxController::class, 'create']);
        Route::any('callback', [PaytmSandboxController::class, 'callback']);
    });
});

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
    Route::post('success', [PayuController::class, 'success']);
    Route::post('failed', [PayuController::class, 'failed']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [PayuSandboxController::class, 'request']);
        Route::post('success', [PayuSandboxController::class, 'success']);
        Route::post('failed', [PayuSandboxController::class, 'failed']);
    });
});

Route::prefix('cashfree')->group(function () {
    Route::get('create', [CashfreeController::class, 'create']);
    Route::get('request', [CashfreeController::class, 'request']);
    Route::post('callback', [CashfreeController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('create', [CashfreeSandboxController::class, 'create']);
        Route::get('request', [CashfreeSandboxController::class, 'request']);
        Route::post('callback', [CashfreeSandboxController::class, 'callback']);
    });
});

Route::prefix('instamojo')->group(function () {
    Route::get('request', [InstaMojoController::class, 'request']);
    Route::any('callback', [InstaMojoController::class, 'callback']);

    Route::prefix('sandbox')->group(function () {
        Route::get('request', [InstaMojoSandboxController::class, 'request']);
        Route::any('callback', [InstaMojoSandboxController::class, 'callback']);
    });
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('login', [LoginController::class, 'loginSubmit'])->name('login.submit');

Route::middleware('admin.auth')->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::match(['get', 'post'], 'profile', [DashboardController::class, 'profile'])->name('profile');

    Route::middleware([PermissionMiddleware::class])->group(function () {
        Route::get('company', [CompanyController::class, 'index'])->name('company.index');
        Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::post('company/store', [CompanyController::class, 'store'])->name('company.store');
        Route::get('company/{id}/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('company/update/{id}', [CompanyController::class, 'update'])->name('company.update');
        Route::delete('company/destroy/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');

        Route::get('gateway', [GatewayController::class, 'index'])->name('pg.index');
        Route::get('gateway/create', [GatewayController::class, 'create'])->name('pg.create');
        Route::post('gateway/store', [GatewayController::class, 'store'])->name('pg.store');
        Route::get('gateway/{id}/edit', [GatewayController::class, 'edit'])->name('pg.edit');
        Route::put('gateway/update/{id}', [GatewayController::class, 'update'])->name('pg.update');

        Route::get('button-payment', [SalesController::class, 'btnPayment'])->name('btn.payment');
    });

    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('transaction', [SalesController::class, 'index'])->name('tnx.index');
    Route::get('transaction/show/{id}', [SalesController::class, 'show'])->name('tnx.show');
    Route::post('transaction/update/{id}', [SalesController::class, 'update'])->name('tnx.update');

    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});

// FALLBACK HANDLER
Route::fallback([CommonController::class, 'pageNotFound']);
Route::get('page-non-found', [CommonController::class, 'pageNotFound'])->name('not.found');
Route::get('internal-server-error', [CommonController::class, 'serverError'])->name('server.error');
Route::view('test-payment', 'request_payment');
// Route::any('response', function (Request $request) {
//     dd($request->all());
// });
// Route::post('callback', function (Request $request) {
//     file_put_contents(public_path('response.json'), json_encode($request->all()));
// });
