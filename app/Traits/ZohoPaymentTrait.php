<?php

namespace App\Traits;

trait ZohoPaymentTrait
{
    public function getAccessToken()
    {
        $userDetails = [
            'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'account_id' => env('ZOHO_ACCOUNT_ID'),
            'data_center' => 'in',
        ];

        $url = 'https://accounts.zoho.' . ($userDetails['data_center'] ?? 'in') . '/oauth/v2/token';

        $data = http_build_query([
            'refresh_token' => $userDetails['refresh_token'],
            'client_id' => $userDetails['client_id'],
            'client_secret' => $userDetails['client_secret'],
            'grant_type' => 'refresh_token'
        ]);

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $data,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            $lastError = error_get_last();

            return [
                'status' => false,
                'message' => $lastError['message'] ?? 'Unknown error'
            ];
        }

        $decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['error'])) {

            return [
                'status' => false,
                'message' => $decodedResponse['error_description']
                    ?? $decodedResponse['error']
            ];
        }

        $accessToken = $decodedResponse['access_token'] ?? null;

        if (empty($accessToken)) {
            return [
                'status' => false,
                'message' => 'Zoho token refresh failed.'
            ];
        }

        return [
            'status' => true,
            'access_token' => $accessToken
        ];
    }

    public function createPaymentSession(string $amount, string $orderId)
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken['status']) {

            return null;
        }

        $payload = [
            'amount' => $amount,
            'currency' => 'INR',
            'meta_data' => [
                [
                    'key' => 'order_id',
                    'value' => strtoupper($orderId)
                ]
            ],
            'description' => 'Payment for Order #' . strtoupper($orderId),
            'invoice_number' => 'INV-' . strtoupper($orderId)
        ];

        $options = [
            'http' => [
                'header' =>
                "Content-type: application/json\r\n" .
                    "Authorization: Zoho-oauthtoken {$accessToken['access_token']}\r\n",
                'method' => 'POST',
                'content' => json_encode($payload),
                'ignore_errors' => true,
            ],
        ];

        $context = stream_context_create($options);

        $url = 'https://payments.zoho.in/api/v1/paymentsessions?account_id=' . env('ZOHO_ACCOUNT_ID');

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        $decoded = json_decode($response, true);

        if (isset($decoded['message']) && $decoded['message'] === 'success') {

            return $decoded;
        }

        return null;
    }

    /**
     * Method 2: Complete Payment
     */
    public function completePayment(array $payload, array $userDetails)
    {
        $accessToken = $userDetails['access_token'] ?? '';

        if (!empty($accessToken)) {
            $authToken = $accessToken;
        } else {

            $tokenResult = $this->getAccessToken($userDetails);

            if (!$tokenResult['status']) {
                return $tokenResult;
            }

            $authToken = $tokenResult['access_token'];
        }

        return $this->tryPaymentSession($payload, $userDetails, 1, $authToken);
    }

    /**
     * Method 3: Try Payment Session (Retry Logic)
     */
    public function tryPaymentSession(array $payload, array $userDetails, int $try, string $authToken)
    {
        if ($try > 3) {
            return [
                'status' => false,
                'message' => 'Payment session failed after 3 attempts.'
            ];
        }

        $accountId = $userDetails['account_id'];

        $url = "https://payments.zoho.in/api/v1/paymentlinks?account_id={$accountId}";

        $options = [
            'http' => [
                'method' => 'POST',
                'header' =>
                "Authorization: Zoho-oauthtoken {$authToken}\r\n" .
                    "Content-Type: application/json\r\n",
                'content' => json_encode($payload),
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {

            // refresh token and retry
            $newTokenResult = $this->getAccessToken($userDetails);

            if (!$newTokenResult['status']) {
                return $newTokenResult;
            }

            return $this->tryPaymentSession(
                $payload,
                $userDetails,
                $try + 1,
                $newTokenResult['access_token']
            );
        }

        $result = json_decode($response, true);

        if (isset($result['code']) && strtolower($result['code']) === 'error') {

            // retry token refresh
            $newTokenResult = $this->getAccessToken($userDetails);

            if (!$newTokenResult['status']) {
                return $newTokenResult;
            }

            return $this->tryPaymentSession(
                $payload,
                $userDetails,
                $try + 1,
                $newTokenResult['access_token']
            );
        }

        return [
            'status' => true,
            'response' => $result
        ];
    }
}
