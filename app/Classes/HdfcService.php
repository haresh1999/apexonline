<?php

namespace App\Classes;

use Exception;

class HdfcService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $merchantId;
    protected string $paymentPageClientId;
    protected string $apiVersion;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('HDFC_BASE_URL'), '/');
        $this->apiKey = env('HDFC_API_KEY');
        $this->merchantId = env('HDFC_MERCHANT_ID');
        $this->paymentPageClientId = env('HDFC_CLIENT_ID');
        $this->apiVersion = env('HDFC_VERSION');
    }

    /**
     * Create HDFC SmartGateway payment session
     */
    public function createOrderSession(array $data): array
    {
        $payload = [
            'order_id'               => $data['order_id'],
            'amount'                 => $data['amount'], // usually in paise
            'customer_id'            => $data['customer_id'],
            'customer_email'         => $data['customer_email'] ?? null,
            'customer_phone'         => $data['customer_phone'] ?? null,
            'payment_page_client_id' => $data['payment_page_client_id'] ?? $this->paymentPageClientId,
            'action'                 => 'paymentPage',
            'return_url'             => $data['return_url'],
            'description'            => $data['description'] ?? null,
            'first_name'             => $data['first_name'] ?? null,
            'last_name'              => $data['last_name'] ?? null,
        ];

        // remove null values
        $payload = array_filter($payload, fn($v) => !is_null($v));

        return $this->request('/session', 'POST', $payload, 'application/json');
    }

    /**
     * Fetch order status
     */
    public function orderStatus(string $orderId): array
    {
        return $this->request("/orders/{$orderId}", 'GET');
    }

    /**
     * Generic cURL request based on plugin logic
     */
    protected function request(string $path, string $method = 'POST', array $payload = [], string $contentType = 'application/json'): array
    {
        $url = $this->baseUrl . $path;

        $ch = curl_init();

        if (!$ch) {
            throw new Exception('Unable to initialize cURL');
        }

        $headers = [
            'version: ' . $this->apiVersion,
            'x-merchantid: ' . $this->merchantId,
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);

        // IMPORTANT: plugin uses basic auth with api key
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($ch, CURLOPT_USERAGENT, 'LARAVEL_HDFC/' . $this->merchantId);

        if ($method === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);

            if (!empty($payload)) {
                $url .= '?' . http_build_query($payload);
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        } else {
            curl_setopt($ch, CURLOPT_POST, true);

            if ($contentType === 'application/json') {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            } else {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('HDFC cURL error: ' . $error);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $responseHeaders = substr($response, 0, $headerSize);
        $responseBodyRaw = substr($response, $headerSize);
        $responseBody    = json_decode($responseBodyRaw, true);

        curl_close($ch);

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'headers' => $responseHeaders,
            'body_raw' => $responseBodyRaw,
            'data' => $responseBody,
        ];
    }
}
