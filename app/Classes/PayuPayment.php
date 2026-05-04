<?php

namespace App\Classes;

class PayuPayment
{
    public function generateHash($params, $salt)
    {
        // Extract parameters or use empty string if not provided
        $key = $params['key'];
        $txnid = $params['txnid'];
        $amount = $params['amount'];
        $productinfo = $params['productinfo'];
        $firstname = $params['firstname'];
        $email = $params['email'];
        $udf1 = isset($params['udf1']) ? $params['udf1'] : '';
        $udf2 = isset($params['udf2']) ? $params['udf2'] : '';
        $udf3 = isset($params['udf3']) ? $params['udf3'] : '';
        $udf4 = isset($params['udf4']) ? $params['udf4'] : '';
        $udf5 = isset($params['udf5']) ? $params['udf5'] : '';

        // Construct hash string with exact parameter sequence
        $hashString = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' .
            $firstname . '|' . $email . '|' . $udf1 . '|' . $udf2 . '|' .
            $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $salt;

        // Generate hash and convert to lowercase
        return strtolower(hash('sha512', $hashString));
    }

    public function verifyPayuResponse($params, $salt)
    {
        // Check if all required parameters exist
        if (!isset(
            $params['status'],
            $params['txnid'],
            $params['amount'],
            $params['productinfo'],
            $params['firstname'],
            $params['email'],
            $params['key'],
            $params['hash']
        )) {
            return false;
        }

        // Create hash string with exact parameter sequence 
        // Reverse hash format for response validation:
        // sha512(SALT|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)

        $hashString = $salt . '|' . $params['status'] . '||||||';

        // Add UDFs if present
        $hashString .= (isset($params['udf5']) ? $params['udf5'] : '') . '|';
        $hashString .= (isset($params['udf4']) ? $params['udf4'] : '') . '|';
        $hashString .= (isset($params['udf3']) ? $params['udf3'] : '') . '|';
        $hashString .= (isset($params['udf2']) ? $params['udf2'] : '') . '|';
        $hashString .= (isset($params['udf1']) ? $params['udf1'] : '') . '|';

        // Add remaining fields
        $hashString .= $params['email'] . '|' .
            $params['firstname'] . '|' .
            $params['productinfo'] . '|' .
            $params['amount'] . '|' .
            $params['txnid'] . '|' .
            $params['key'];

        // Calculate hash
        $calculatedHash = strtolower(hash('sha512', $hashString));

        // Compare the calculated hash with the hash received in the response
        return $calculatedHash === $params['hash'];
    }
}
