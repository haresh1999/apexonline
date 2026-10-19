<?php

class GatewaySDK
{
    private string $apiUrl;
    private string $token;
    private string $secretKey;

    /**
     * Constructor for SDK
     * 
     * @param string $apiUrl The base URL of the payment gateway (e.g., https://yourdomain.com/gateways)
     * @param string $token Merchant Token
     * @param string $secretKey Merchant Secret Key for encryption
     */
    public function __construct($apiUrl, $token, $secretKey)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->token = $token;
        $this->secretKey = $secretKey;
    }

    /**
     * Create an encrypted payload and return an auto-submitting HTML form
     * 
     * @param float $amount The amount to charge
     * @param string $userPhone The customer's mobile number
     * @param string $udf1 User defined field (optional)
     * @param string $returnUrl URL where customer should be redirected after payment (optional)
     * @return string HTML form
     */
    public function createOrderForm($amount, $userPhone, $udf1 = '', $returnUrl = '')
    {
        $data = [
            'amount' => $amount,
            'mobile' => $userPhone,
            'udf1' => $udf1
        ];

        if (!empty($returnUrl)) {
            $data['return_url'] = $returnUrl;
        }

        $json = json_encode($data);

        // Encrypting using AES-256-ECB as per create_order.php
        $encrypted = openssl_encrypt($json, 'aes-256-ecb', $this->secretKey, OPENSSL_RAW_DATA);
        if ($encrypted === false) {
            throw new Exception("Encryption failed");
        }

        $payload = base64_encode($encrypted);

        $html = '<!DOCTYPE html>
            <html>
            <head>
                <title>Redirecting to Payment...</title>
                <style>body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f9; }</style>
            </head>
            <body>
                <h2>Please wait, redirecting to secure payment...</h2>
                <form id="gatewayForm" action="' . $this->apiUrl . '/create_order.php" method="GET" style="display:none;">
                    <input type="hidden" name="token" value="' . htmlspecialchars($this->token) . '">
                    <input type="hidden" name="payload" value="' . htmlspecialchars($payload) . '">
                </form>
                <script>
                    document.getElementById("gatewayForm").submit();
                </script>
            </body>
            </html>';

        return $html;
    }

    /**
     * Verify the callback received from the payment gateway
     * 
     * @param array $postData The $_POST array received from gateway
     * @return array|false Returns decrypted array if valid, false otherwise
     */
    public function verifyCallback($postData)
    {
        // Checking if encrypted payload is sent in callback
        if (!empty($postData['payload'])) {
            $payload_str = str_replace(' ', '+', $postData['payload']);
            $decrypted = openssl_decrypt(base64_decode($payload_str), 'aes-256-ecb', $this->secretKey, OPENSSL_RAW_DATA);

            if ($decrypted !== false) {
                return json_decode($decrypted, true);
            }
        }

        // Alternatively, if it uses signature/hash matching (Basic fallback)
        if (!empty($postData['order_id']) && !empty($postData['status']) && !empty($postData['hash'])) {
            $generated_hash = md5($postData['order_id'] . $postData['status'] . $this->secretKey);
            if ($generated_hash === $postData['hash']) {
                return $postData;
            }
        }

        return false;
    }
}
