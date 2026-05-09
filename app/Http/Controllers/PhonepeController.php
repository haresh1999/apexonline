<?php

namespace App\Http\Controllers;

use App\Models\PhonepeOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PhonepeController extends Controller
{
    private function getAccessToken()
    {
        $url = 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token';

        $fields = [
            'client_id' => setting('phonepe', 'client_id'),
            'client_version' => setting('phonepe', 'client_version'),
            'client_secret' => setting('phonepe', 'client_secret'),
            'grant_type' => setting('phonepe', 'grant_type')
        ];

        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($response);

        return $response->access_token;
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

        $actionUrl = url('phonepe/redirect');

        return view('phonepe.request', compact('actionUrl', 'transaction'));
    }

    public function redirect(Request $request)
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

        $payload = [
            'merchantOrderId' => $transaction->order_id,
            'amount' => ($transaction->amount * 100),
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'message' => 'Proceed to complete the payment',
                'merchantUrls' => [
                    'redirectUrl' => url('phonepe/callback') . "?ref_id={$transaction->reference_id}",
                ],
            ],
        ];

        $accessToken = $this->getAccessToken();

        $url = 'https://api.phonepe.com/apis/pg/checkout/v2/pay';

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: O-Bearer ' . $accessToken,
            ],
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $redirectUrl = json_decode($response, true);

        if (isset($redirectUrl['redirectUrl'])) {

            $redirectTo = $redirectUrl['redirectUrl'];

            return redirect()->to($redirectTo);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create payment link',
            'details' => $response
        ], 401);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => 'required',
        ]);

        $transaction = Transaction::where('reference_id', $request->ref_id)
            ->where('gateway', 'phonepe')
            ->where('env', 'production')
            ->first();

        if (!$transaction) {
            return response()->json([
                'error' => 'Transaction not found'
            ], 404);
        }

        // already processed
        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $url = "https://api.phonepe.com/apis/pg/checkout/v2/order/{$transaction->order_id}/status";

        $accessToken = $this->getAccessToken();

        $headers = [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken,
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);

        curl_close($ch);

        // curl failed
        if ($curlError) {

            $transaction->update([
                'status' => 'failed',
                'payment_response' => $curlError,
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $result = json_decode($response, true);

        if (!$result) {

            $transaction->update([
                'status' => 'failed',
                'payment_response' => $response,
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $state = strtolower(
            $result['state'] ?? 'failed'
        );

        $paymentStatus = match ($state) {
            'completed' => 'completed',
            'pending' => 'pending',
            default => 'failed',
        };

        $transaction->update([
            'status' => $paymentStatus,
            'payment_response' => json_encode($result),
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
