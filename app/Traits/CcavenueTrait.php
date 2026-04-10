<?php

namespace App\Traits;

trait CcavenueTrait
{
    public function pkcs5Pad($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);

        return $plainText . str_repeat(chr($pad), $pad);
    }

    public function hextobin($hexString)
    {
        return hex2bin($hexString);
    }

    public function encrypts($plainText, $key)
    {
        $secretKey = substr(hash('md5', $key, true), 0, 16); // 16-byte key for AES-128

        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

        $plainPad = $this->pkcs5Pad($plainText, 16);

        $encryptedText = openssl_encrypt($plainPad, 'aes-128-cbc', $secretKey, OPENSSL_RAW_DATA, $initVector);

        return bin2hex($encryptedText);
    }

    public function decrypts($encryptedText, $key)
    {
        $secretKey = substr(hash('md5', $key, true), 0, 16); // 16-byte key for AES-128

        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

        $encryptedText = $this->hextobin($encryptedText);

        $decryptedText = openssl_decrypt($encryptedText, 'aes-128-cbc', $secretKey, OPENSSL_RAW_DATA, $initVector);

        return rtrim($decryptedText, "\0");
    }
}
