<?php

namespace App\Http\Controllers;

use App\Classes\PaytmChecksum;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaytmController extends Controller
{
    private function createOrder($transaction)
    {
        $mid = setting('paytm', 'mid');
        $merchantKey = setting('paytm', 'mkey');
        $website = setting('paytm', 'website');

        $orderId = 'ORD_' . strtoupper($transaction->order_id);
        $amount = number_format($transaction->amount, 2, '.', '');
        $custId = "CUST_" . rand(11111, 99999);

        $paytmParams = array();

        $paytmParams["body"] = array(
            "requestType"  => "Payment",
            "mid"      => $mid,
            "websiteName"  => $website,
            "orderId"    => $orderId,
            "callbackUrl"  => url('paytm/callback?ref_id=' . $transaction->reference_id),
            "txnAmount"   => array(
                "value"   => $amount,
                "currency" => "INR",
            ),
            "userInfo"   => array(
                "custId"  => $custId,
            ),
        );

        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $merchantKey);

        $paytmParams["head"] = array(
            "signature" => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        $url = "https://secure.paytmpayments.com/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderId";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            return [
                'status' => false,
                'message' => $curlError
            ];
        }

        $responseBody = json_decode($response, true);

        $transaction->update([
            'payment_response' => json_encode($responseBody)
        ]);

        if ($httpCode == 200 && isset($responseBody['body']['txnToken']) && $responseBody['body']['resultInfo']['resultStatus'] === 'S') {

            return [
                'status' => true,
                'orderId' => $orderId,
                'txnToken' => $responseBody['body']['txnToken'],
                'amount' => $amount
            ];
        }

        return [
            'status' => false,
            'message' => $responseBody['body']['resultInfo']['resultMsg'] ?? 'Paytm transaction failed',
            'response' => $responseBody
        ];
    }

    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'production');
            })]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first());
        }

        $transaction = Transaction::where('reference_id', $request->reference_id)->first();

        $token = $this->createOrder($transaction);

        if (! $token['status']) {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        return view('paytm.request', compact('transaction', 'token'));
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => 'required'
        ]);

        $transaction = Transaction::where('reference_id', $request->ref_id)
            ->where('gateway', 'paytm')
            ->where('env', 'production')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $mid = setting('paytm', 'mid');
        $merchantKey = setting('paytm', 'mkey');

        $paytmParams = array();

        $paytmParams = array(
            "MID" => $mid,
            "ORDERID" => 'ORD_' . strtoupper($transaction->order_id),
        );

        $paytmParams['CHECKSUMHASH'] = PaytmChecksum::generateSignature(json_encode($paytmParams, JSON_UNESCAPED_SLASHES), $merchantKey);

        // Correct Production URL
        $url = "https://secure.paytmpayments.com/order/status";

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withOptions(['verify' => false])
            ->send('POST', $url, ['body' => json_encode($paytmParams, JSON_UNESCAPED_SLASHES)]);

        $responseBody = $response->json();

        if (isset($responseBody['STATUS']) == 'TXN_SUCCESS') {

            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($responseBody)
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => 'failed',
            'payment_response' => json_encode($responseBody)
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
