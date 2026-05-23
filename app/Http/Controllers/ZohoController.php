<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZohoController extends Controller
{
    public function request()
    {
        $accessToken = env('ZOHO_ACCESS_TOKEN');
        $accountId = env('ZOHO_ACCOUNT_ID');

        $payload = [
            "amount" => 1000, // Rs.1000
            "currency" => "INR",
            "description" => "Test Payment",
            "customer" => [
                "name" => "Haresh Chauhan",
                "email" => "test@gmail.com",
                "phone" => "9876543210"
            ],
            "redirect_url" => "https://apexonline.in/"
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://payments.zoho.in/api/v1/paymentlinks?account_id={$accountId}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Zoho-oauthtoken {$accessToken}",
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return response()->json(['error' => $error]);
        }

        dd($response);

        return response()->json(json_decode($response, true));
    }
}
