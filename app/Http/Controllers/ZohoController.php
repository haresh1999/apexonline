<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\ZohoPaymentTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ZohoController extends Controller
{
    use ZohoPaymentTrait;

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

        $amount = $transaction->amount;
        $mobile = $transaction->payer_mobile;
        $orderId = strtoupper($transaction->order_id);
        $description = 'Payment for Order #' . strtoupper($orderId);

        $response = $this->createPaymentSession($amount, $orderId);

        if (!$response) {

            return response()->json(['error' => 'Payment session creation failed'], 500);
        }

        $transaction->update([
            'payment_response' => json_encode($response)
        ]);

        return view('zoho.request', [
            'accountId' => env('ZOHO_ACCOUNT_ID'),
            'paymentSessionId' => $response['payments_session']['payments_session_id'],
            'amount' => $amount,
            'currency' => 'INR',
            'business' => 'ApexOnline',
            'description' => $description,
            'phone' => $mobile,
            'orderId' => $orderId,
            'reference_id' => $transaction->reference_id
        ]);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => 'required',
            'payment_id' => 'required',
        ]);

        $transaction = Transaction::where('reference_id', $request->ref_id)
            ->where('gateway', 'zoho')
            ->where('env', 'production')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $accessToken = $this->getAccessToken();

        if (!$accessToken['status']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve access token'
            ], 500);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://payments.zoho.in/api/v1/payments/{$request->payment_id}?account_id=" . env('ZOHO_ACCOUNT_ID'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Zoho-oauthtoken " . $accessToken['access_token'],
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $update['payment_response'] = $response;

        if ($err) {
            $update['status'] = 'failed';
        } else {
            $response = json_decode($response, true);
            $update['status'] = isset($response['payment']['status']) && $response['payment']['status'] === 'succeeded' ? 'completed' : 'failed';
        }

        $transaction->update($update);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
