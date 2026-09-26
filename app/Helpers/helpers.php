<?php

function ids($id)
{
    return sprintf('%05d', $id);
}

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


function getUserId()
{
    if (auth()->user()->user_id == null) {

        return auth()->id();
    }

    return auth()->user()->user_id;
}


function pdfToBase64(string $pdfPath): string
{
    if (!file_exists($pdfPath)) {
        throw new Exception("PDF file not found.");
    }

    return base64_encode(file_get_contents($pdfPath));
}

function base64ToPdf(string $base64, string $outputPath): bool
{
    // Remove data URI prefix if present
    if (str_contains($base64, ',')) {
        $base64 = explode(',', $base64, 2)[1];
    }

    $pdfContent = base64_decode($base64, true);

    if ($pdfContent === false) {
        throw new Exception("Invalid Base64 data.");
    }

    file_put_contents($outputPath, $pdfContent);

    return true;
}


function amountInWords($amount)
{
    $amount = (int) $amount;

    if ($amount == 0) {
        return 'Zero Rupees Only';
    }

    $ones = [
        '',
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
        'Seven',
        'Eight',
        'Nine',
        'Ten',
        'Eleven',
        'Twelve',
        'Thirteen',
        'Fourteen',
        'Fifteen',
        'Sixteen',
        'Seventeen',
        'Eighteen',
        'Nineteen'
    ];

    $tens = [
        '',
        '',
        'Twenty',
        'Thirty',
        'Forty',
        'Fifty',
        'Sixty',
        'Seventy',
        'Eighty',
        'Ninety'
    ];

    $convert = function ($number) use (&$convert, $ones, $tens) {

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return $tens[(int)($number / 10)] .
                (($number % 10) ? ' ' . $ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return $ones[(int)($number / 100)] . ' Hundred' .
                (($number % 100) ? ' ' . $convert($number % 100) : '');
        }

        if ($number < 100000) {
            return $convert((int)($number / 1000)) . ' Thousand' .
                (($number % 1000) ? ' ' . $convert($number % 1000) : '');
        }

        if ($number < 10000000) {
            return $convert((int)($number / 100000)) . ' Lakh' .
                (($number % 100000) ? ' ' . $convert($number % 100000) : '');
        }

        return $convert((int)($number / 10000000)) . ' Crore' .
            (($number % 10000000) ? ' ' . $convert($number % 10000000) : '');
    };

    return $convert($amount) . ' Rupees Only';
}
