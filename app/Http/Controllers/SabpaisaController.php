<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SabpaisaController extends Controller
{
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

        try {

            $transaction = Transaction::where('reference_id', $request->reference_id)->first();

            $orderId = $transaction->order_id;
            $amount = $transaction->amount * 100;
            $timestamp = time();
            $merchantTxnId = strtoupper('PRO' . $orderId . $timestamp);

            $merchantId = setting('sabpaisa', 'client_code');
            $secretKey = setting('sabpaisa', 'secret');
            $apiKey = setting('sabpaisa', 'api_key');

            $apiUrl = 'https://merchant-api.sabpaisa.in/api/v2/payments';

            $checksumString = $merchantId . "|" . $merchantTxnId . "|" . $amount . "|INR|" . $timestamp;

            $checksum = hash_hmac('sha256', $checksumString, $secretKey);

            $payload = [
                'merchantId' => $merchantId,
                'merchantTxnId' => $merchantTxnId,
                'amount' => (int) $amount,
                'currency' => 'INR',
                'customerName' => $transaction->payer_name,
                'customerEmail' => $transaction->payer_email,
                'customerPhone' => $transaction->payer_phone,
                'returnUrl' => url('sabpaisa/callback?ref_id=' . $transaction->reference_id),
                'description' => 'Order #' . $orderId,
                'checksum' => $checksum,
                'timestamp' => $timestamp
            ];

            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])
                ->timeout(60)
                ->withoutVerifying()
                ->post($apiUrl, $payload);

            $body = $response->json();

            $transaction->update([
                'payment_response' => json_encode($body)
            ]);

            if ($response->status() == 201 && isset($body['checkoutUrl']) && isset($body['clientSecret'])) {

                $redirectUrl = $body['checkoutUrl'] . '?clientSecret=' . $body['clientSecret'];

                return redirect()->to($redirectUrl);
            }

            return response()->json([
                'status' => false,
                'message' => 'Payment initialization failed',
                'response' => $body
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'SabPaisa connection failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function callback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_id' => ['required', Rule::exists('transactions', 'reference_id')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'production');
            })],
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first());
        }

        $transaction = Transaction::where('reference_id', $request->ref_id)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $status = isset($_GET['status']) ? strtoupper($_GET['status']) : 'FAILED';

        if ($status === 'SUCCESS') {

            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($request->all()),
            ]);
        } else {

            $transaction->update([
                'status' => 'failed',
                'payment_response' => json_encode($request->all()),
            ]);
        }

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
