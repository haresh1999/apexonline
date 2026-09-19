<?php

namespace App\Traits;

trait UpiPaymentTrait
{
    function encryptTouras($text, $key)
    {
        $iv = "0123456789abcdef";
        $size = 16;

        $pad = $size - (strlen($text) % $size);

        $padtext = $text . str_repeat(chr($pad), $pad);

        $crypt = openssl_encrypt(
            $padtext,
            "AES-256-CBC",
            base64_decode($key),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        return base64_encode($crypt);
    }

    function decryptTouras($crypt, $key)
    {
        $iv = "0123456789abcdef";

        $crypt = base64_decode($crypt);

        $padtext = openssl_decrypt(
            $crypt,
            "AES-256-CBC",
            base64_decode($key),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        if ($padtext === false || $padtext === '') {
            return false;
        }

        $pad = ord($padtext[strlen($padtext) - 1]);

        if ($pad > strlen($padtext)) {
            return false;
        }

        if (
            strspn(
                $padtext,
                $padtext[strlen($padtext) - 1],
                strlen($padtext) - $pad
            ) != $pad
        ) {
            return false;
        }

        return substr($padtext, 0, -1 * $pad);
    }

    function createTourasHash($merchantId, $orderNo, $amount, $country, $currency, $encryptionKey)
    {
        $hashString =
            $merchantId . '~' .
            $orderNo . '~' .
            $amount . '~' .
            $country . '~' .
            $currency;

        // SHA-256
        $sha256 = hash('sha256', $hashString);

        // AES-256 encrypt hash
        return $this->encryptTouras($sha256, $encryptionKey);
    }
}
