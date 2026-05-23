<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ZohoSandboxController extends Controller
{
    public function createPaymentLink()
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
            "redirect_url" => "https://yourdomain.com/payment-success"
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

        return response()->json(json_decode($response, true));
    }

    public function zohoTest()
    {
        $apiKey = env('ZOHO_SANDBOX_API_KEY');
        $signingKey = env('ZOHO_SANDBOX_SIGNING_KEY');

        $payload = [
            'amount' => 100,
            'currency' => 'INR',
            'purpose' => 'Test Payment',
            'reference_id' => 'ORD_' . time(),
        ];

        // JSON encode payload
        $zohoInputStream = base64_encode(json_encode($payload));

        // Generate signature
        $signature = hash_hmac('sha256', $zohoInputStream, $signingKey);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://payments.zoho.in/api/v1/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'X-API-KEY: ' . $apiKey,
                'X-SIGNATURE: ' . $signature,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'zoho-inputstream' => $zohoInputStream
            ]),
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        dd($response);

        return response()->json([
            'http_code' => $httpCode,
            'error' => $error,
            'response' => json_decode($response, true),
            'raw' => $response,
        ]);
    }

    // private function getAccessToken()
    // {
    //     return;
    // }

    // public function request(Request $request)
    // {
    //     $payload = [
    //         "amount" => 100,
    //         "currency" => "INR",
    //         "description" => "Test Payment",
    //         "customer" => [
    //             "name" => "Haresh",
    //             "email" => "test@gmail.com"
    //         ]
    //     ];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => "https://payments.zoho.in/api/v1/payments",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST => true,
    //         CURLOPT_HTTPHEADER => [
    //             "Authorization: Bearer YOUR_API_KEY", // or signing key auth
    //             "Content-Type: application/json",
    //         ],
    //         CURLOPT_POSTFIELDS => json_encode($payload),
    //     ]);

    //     $response = curl_exec($curl);

    //     if (curl_errno($curl)) {
    //         return curl_error($curl);
    //     }

    //     curl_close($curl);

    //     return json_decode($response, true);



    //     // $token = $this->getAccessToken();

    //     // dd($token0);

    //     // $validator = Validator::make($request->all(), [
    //     //     'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
    //     //         $q->where('status', 'pending')->where('env', 'sandbox');
    //     //     })]
    //     // ]);

    //     // if ($validator->fails()) {
    //     //     return response()->json([
    //     //         'status' => false,
    //     //         'message' => $validator->errors()->first()
    //     //     ], 422);
    //     // }

    //     // $input = $validator->validated();

    //     // $transaction = Transaction::where('reference_id', $input['reference_id'])->first();
    // }

    // public function callback() {}
}
