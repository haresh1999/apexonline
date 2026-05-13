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
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where('status', 'pending')->where('env', 'production')]
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $mid = setting('paytm', 'mid');
        $merchantKey = setting('paytm', 'mkey');
        $website = setting('paytm', 'website');

        $orderId = strtoupper('ORD_' . $transaction->order_id);
        $amount = number_format($transaction->amount, 2, '.', '');
        $custId = "CUST_" . rand(11111, 99999);

        $body = [
            "requestType" => "Payment",
            "mid" => $mid,
            "websiteName" => $website,
            "orderId" => $orderId,
            "callbackUrl" => url('paytm/callback'),
            "txnAmount" => [
                "value" => $amount,
                "currency" => "INR"
            ],
            "userInfo" => [
                "custId" => $custId
            ]
        ];

        $checksum = PaytmChecksum::generateSignature(
            json_encode($body, JSON_UNESCAPED_SLASHES),
            $merchantKey
        );

        $response = Http::asJson()
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(
                "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderId",
                [
                    "body" => $body,
                    "head" => [
                        "signature" => $checksum
                    ]
                ]
            );

        // dd($response->json());

        $transaction->update(['payment_response' => json_encode($request->json())]);

        if ($response->successful()) {

            $responseBody = $response->json();

            if (isset($responseBody['body']['txnToken']) && $responseBody['body']['resultInfo']['resultStatus'] === 'S') {

                return response()->json([
                    'status' => true,
                    'orderId' => $orderId,
                    'txnToken' => $responseBody['body']['txnToken'],
                    'amount' => $transaction->amount
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $responseBody['body']['resultInfo']['resultMsg'] ?? 'Paytm error'
            ], 400);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to create payment',
        ], 401);
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

        $createOrderUrl = url('paytm/create?reference_id=' . $transaction->reference_id);

        return view('paytm.request', compact('createOrderUrl', 'transaction'));
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

        $orderId = $request->ORDERID;

        $body = [
            "mid" => $mid,
            "orderId" => $orderId
        ];

        $checksum = PaytmChecksum::generateSignature(
            json_encode($body),
            $merchantKey
        );

        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post(
            "https://securegw.paytm.in/v3/order/status",
            [
                "body" => $body,
                "head" => [
                    "signature" => $checksum
                ]
            ]
        );

        $responseBody = $response->json();

        if (isset($responseBody['body']['resultInfo']['resultStatus']) && $responseBody['body']['resultInfo']['resultStatus'] === 'TXN_SUCCESS') {

            $orderId = str_replace('ORD_', '', $orderId);

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
