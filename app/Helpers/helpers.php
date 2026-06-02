<?php

function setting($pg, $key)
{
    $env = getAppEnv();

    return config("services.{$pg}.{$env}.{$key}");
}

function getPaymentGateway($amount)
{
    return match (true) {
        $amount >= 1000 && $amount < 3000 => 'phonepe',
        $amount >= 3000 && $amount < 5000 => 'paytm',
        $amount >= 5000 && $amount < 10000 => 'payu',
        $amount >= 10000 && $amount < 25000 => 'cashfree',
        $amount >= 25000 && $amount <= 50000 => 'razorpay',
        default => 'not_available',
    };
}

function getAppEnv()
{
    $currentUrl = url()->current();

    return str_contains($currentUrl, 'sandbox') ? 'sandbox' : 'production';
}

function navbar($route)
{
    if (is_array($route)) {
        foreach ($route as $value) {
            if (request()->is("{$value}")) {
                return 'active';
            }
        }
        return '';
    }

    return request()->is("{$route}") ? 'active' : '';
}

function gatewayList()
{
    return [
        'phonepe',
        'razorpay',
        'cashfree',
        'payu',
        'easebuzz',
        'paytm',
        'zaaakapay',
        'ccavenue',
        'zoho',
        'instamojo',
        'sabpaisa'
    ];
}

function generateStrongPassword($length = 12)
{
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers   = '0123456789';
    $special   = '!@#$%^&*()_+-=';

    $password = '';
    $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];

    $allChars = $uppercase . $lowercase . $numbers . $special;

    for ($i = strlen($password); $i < $length; $i++) {
        $password .= $allChars[random_int(0, strlen($allChars) - 1)];
    }

    return str_shuffle($password);
}
