<?php

namespace App\Classes;

class PayuPayment
{
    public function generateHash($data)
    {
        $hashString = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' . $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . env('PAYU_SALT');

        return strtolower(hash('sha512', $hashString));
    }
}
