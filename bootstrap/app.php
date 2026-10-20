<?php

use App\Http\Middleware\{
    AuthMiddleware,
    SignatureMiddleware,
    TnxLimitMiddleware,
    TokenMiddleware,
};

use App\Http\Middleware\Admin\AuthMiddleware as AdminAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{
    Exceptions,
    Middleware
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthMiddleware::class,
            'auth' => AuthMiddleware::class,
            'signature' => SignatureMiddleware::class,
            'token' => TokenMiddleware::class,
            'tnx.limit' => TnxLimitMiddleware::class,
        ])->validateCsrfTokens(except: [
            'token',
            'sandbox/token',
            'request',
            'sandbox/request',
            'status',
            'sandbox/status',

            'hdfc/callback',
            'hdfc/sandbox/callback',
            'payu/success',
            'payu/sandbox/success',
            'payu/failed',
            'payu/sandbox/failed',
            'paytm/callback',
            'paytm/sandbox/callback',

            'esign/webhook/*',

            'upi/callback/*',
            'upi/sandbox/callback/*',

            'oneroyal/callback',
            'apexonline/callback',

            'paisapay/payout/status'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
