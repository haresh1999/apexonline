<?php

namespace App\Http\Controllers;

use App\Classes\PaisaPay;
use Illuminate\Http\Request;

class PaisaPayController extends Controller
{
    public function create()
    {
        // Configure your API details here
        $API_BASE_URL = "https://pay.paisapay.site"; // Actual API URL
        $MERCHANT_TOKEN = "2411a9a7-3729-4b37-a1df-e02f7f1de569";
        $MERCHANT_SECRET = "b5a81b0062684faf5ed18e042ca9069b";

        $sdk = new PaisaPay(
            $API_BASE_URL,
            $MERCHANT_TOKEN,
            $MERCHANT_SECRET,
        );


        // Payment details
        $amount = 100;
        $userPhone = "9106029220";
        $orderId = "MY_ORDER_" . time();

        // Generate and output the redirect form
        echo $sdk->createOrderForm($amount, $userPhone, $orderId);
    }
}
