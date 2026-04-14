<?php

namespace App\Classes;

use Juspay\JuspayEnvironment;
use Juspay\Model\JuspayJWT;
use Juspay\Model\Order;
use Juspay\Model\OrderSession;
use Juspay\RequestOptions;
use Juspay\Exception\JuspayException;

class HdfcPayment
{
    public function __construct()
    {
        $config = config('hdfc');

        $privateKey = file_get_contents($config['private_key_path']);
        $publicKey  = file_get_contents($config['public_key_path']);

        if (!$privateKey || !$publicKey) {
            throw new \Exception("Key files missing");
        }

        JuspayEnvironment::init()
            ->withBaseUrl($config['base_url'])
            ->withMerchantId($config['merchant_id'])
            ->withJuspayJWT(new JuspayJWT(
                $config['key_uuid'],
                $publicKey,
                $privateKey
            ));
    }

    public function createSession($orderId, $amount, $customerId, $redirectUrl)
    {
        try {

            $params = [
                "order_id" => $orderId,
                "amount" => $amount,
                "customer_id" => $customerId,
                "payment_page_client_id" => config('hdfc.merchant_id'),
                "action" => "paymentPage",
                "return_url" => $redirectUrl,
            ];

            $requestOption = new RequestOptions();
            $requestOption->withCustomerId($customerId);
            $session = OrderSession::create($params, $requestOption);

            return [
                "success" => true,
                "data" => $session
            ];
        } catch (JuspayException $e) {
            return [
                "success" => false,
                "error" => $e->getErrorMessage()
            ];
        }
    }

    public function orderStatus($orderId, $customerId)
    {
        try {
            $params = ["order_id" => $orderId];

            $requestOption = new RequestOptions();
            $requestOption->withCustomerId($customerId);

            $order = Order::status($params, $requestOption);

            return [
                "success" => true,
                "data" => $order
            ];
        } catch (JuspayException $e) {
            return [
                "success" => false,
                "error" => $e->getErrorMessage()
            ];
        }
    }
}
