<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CashfreeSandboxController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where('status', 'pending')->where('env', 'sandbox')]
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $apiKey = setting('cashfree', 'app_id');
        $secretKey = setting('cashfree', 'secret_key');

        $data = [
            "order_amount" => $transaction->amount,
            "order_currency" => "INR",
            "customer_details" => [
                "customer_id" => 'CUS_' . $transaction->reference_id,
                "customer_name" => $transaction->payer_name,
                "customer_email" => $transaction->payer_email,
                "customer_phone" => $transaction->payer_mobile,
            ],
            "order_meta" => [
                "return_url" => url('cashfree/sandbox/callback') . '?order_id={order_id}' . '&ref_id=' . $transaction->reference_id
            ],
            "order_id" => $transaction->order_id
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://sandbox.cashfree.com/pg/orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-client-id: $apiKey",
                "x-client-secret: $secretKey",
                "x-api-version: 2025-01-01"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $transaction->update([
            'payment_response' => $response
        ]);

        return response()->json(json_decode($response, true));
    }

    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'sandbox');
            })]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first());
        }

        $transaction = Transaction::where('reference_id', $request->reference_id)->first();

        $mode = 'sandbox';
        $orderUrl = url('cashfree/sandbox/create');
        $callbackUrl = url('cashfree/sandbox/callback');
        $statusUrl = url('cashfree/sandbox/status');
        $referenceId = $transaction->reference_id;

        return view('cashfree.request', compact('mode', 'orderUrl', 'callbackUrl', 'statusUrl', 'referenceId'));
    }

    public function callback(Request $request)
    {
        $request->validate([
            'reference_id' => 'required',
            'order_id' => 'required'
        ]);

        $transaction = Transaction::where('reference_id', $request->reference_id)
            ->where('order_id', $request->order_id)
            ->where('gateway', 'cashfree')
            ->where('env', 'sandbox')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $apiKey = setting('cashfree', 'app_id');
        $secretKey = setting('cashfree', 'secret_key');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://sandbox.cashfree.com/pg/orders/{$transaction->order_id}/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "x-client-id: {$apiKey}",
                "x-client-secret: {$secretKey}",
                "x-api-version: 2025-01-01"
            ]
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {

            $transaction->update([
                'status' => 'failed',
                'payment_response' => $error
            ]);

            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $result = json_decode($response, true);

        $paymentStatus = 'failed';

        if (!empty($result)) {

            foreach ($result as $payment) {

                if (isset($payment['payment_status']) && $payment['payment_status'] == 'SUCCESS') {
                    $paymentStatus = 'completed';
                    break;
                }
            }
        }

        $transaction->update([
            'status' => $paymentStatus,
            'payment_response' => json_encode($result)
        ]);

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }
}
