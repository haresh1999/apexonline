<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CashfreeController extends Controller
{
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

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $apiKey = setting('cashfree', 'app_id');
        $secretKey = setting('cashfree', 'secret_key');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cashfree.com/pg/links",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'customer_details' => [
                    'customer_email' => $transaction->payer_email,
                    'customer_name' => $transaction->payer_name,
                    'customer_phone' => $transaction->payer_mobile
                ],
                'link_amount' => $transaction->amount,
                'link_auto_reminders' => true,
                'link_currency' => 'INR',
                'link_expiry_time' => Carbon::now()->addMinutes(10),
                'link_id' => $transaction->reference_id,
                'link_meta' => [
                    'return_url' => url('cashfree/callback?ref_id=') . $transaction->reference_id,
                    'upi_intent' => false
                ],
                'link_partial_payments' => false,
                'link_notes' => [
                    'key_1' => $transaction->reference_id,
                    'key_2' => $transaction->order_id
                ],
                'link_purpose' => 'Order Payment ' . $transaction->order_id,
            ]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-api-version: 2023-08-01",
                "x-client-id: $apiKey",
                "x-client-secret: $secretKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {

            return response()->json('Payment request failed. Try again.', $httpCode);
        }

        $result = json_decode($response, true);

        if (isset($result['link_url'])) {

            return redirect()->to($result['link_url']);
        }

        return response()->json($result['message'], $httpCode);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => [
                'required',
                Rule::exists('transactions', 'reference_id')->where(function ($q) {
                    $q->where('status', 'pending')->where('env', 'production');
                })
            ]
        ]);

        $transaction = Transaction::where('reference_id', $request->ref_id)->first();

        if (!$transaction) {
            return response()->json('Transaction not found');
        }

        $linkId = $transaction->reference_id;

        $apiKey = setting('cashfree', 'app_id');
        $secretKey = setting('cashfree', 'secret_key');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cashfree.com/pg/links/{$linkId}/orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-api-version: 2023-08-01",
                "x-client-id: " . $apiKey,
                "x-client-secret: " . $secretKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {

            $transaction->update([
                'status' => 'failed',
                'response' => json_encode($response)
            ]);

            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $result = json_decode($response, true);

        if (!$result) {

            $transaction->update([
                'status' => 'failed',
                'response' => json_encode($response)
            ]);

            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $orders = $result[0] ?? [];

        if (empty($orders)) {

            $transaction->update([
                'status' => 'failed',
                'response' => json_encode($response)
            ]);

            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $status = strtolower($orders['order_status'] ?? '');

        if ($status == 'paid') {
            $paymentStatus = 'completed';
        } elseif ($status == 'active' || $status == 'pending') {
            $paymentStatus = 'pending';
        } else {
            $paymentStatus = 'failed';
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => $paymentStatus,
            'response' => json_encode($result)
        ]);

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }
}
