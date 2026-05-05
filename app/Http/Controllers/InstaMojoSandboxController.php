<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InstaMojoSandboxController extends Controller
{
    private function getAccessToken()
    {
        $payload = array(
            'grant_type' => 'client_credentials',
            'client_id' => setting('instamojo', 'client_id'),
            'client_secret' => setting('instamojo', 'client_secret')
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.instamojo.com/oauth2/token/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {

            return [
                'status' => false,
                'message' => 'Failed to generate access'
            ];
        }

        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            return $result['access_token'];
        };

        return [
            'status' => false,
            'message' => 'Failed to generate access'
        ];
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

        $input = $validator->validated();

        $token = $this->getAccessToken();

        if (! $token) {

            return response()->json(['message' => $token['message']]);
        }

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $payload = [
            'purpose' => 'Order Payment ' . $transaction->order_id,
            'amount' => $transaction->amount,
            'redirect_url' => url('instamojo/sandbox/callback'),
            'buyer_name' => $transaction->payer_name,
            'email' => $transaction->payer_email,
            'phone' => $transaction->payer_mobile,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.instamojo.com/v2/payment_requests/');
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            return response()->json('Payment request failed. Try again.', $httpCode);
        }

        $result = json_decode($response, true);

        if (isset($result['longurl'])) {

            return redirect()->to($result['longurl']);
        }

        return response()->json('Payment request failed. Try again.', $httpCode);
    }

    public function callback(Request $request)
    {
        $paymentId = $request->input('payment_id');

        if (!$paymentId) {
            return response()->json(['error' => 'Payment ID missing'], 400);
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json(['message' => 'Unable to get access token'], 500);
        }

        $transaction = Transaction::where('reference_id', $request->ref_id)
            ->where('env', 'sandbox')
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token, 'accept' => 'application/json'])
            ->get("https://api.instamojo.com/v2/payments/{$paymentId}");

        if (!$response->successful()) {

            $transaction->update([
                'status' => 'failed',
                'response' => json_encode($response->body())
            ]);

            return response()->json(['error' => 'API failed'], 500);
        }

        $data = $response->json();
        $status = $data['status'] ?? null;

        if (($data['order_info']['order_id'] ?? null) != $transaction->reference_id) {
            abort(403, 'Order mismatch');
        }

        if ($status === true) {

            $transaction->update([
                'status' => 'completed',
                'response' => json_encode($data)
            ]);
        } elseif ($status === false) {

            $transaction->update([
                'status' => 'failed',
                'response' => json_encode($data)
            ]);
        } else {

            $transaction->update([
                'status' => 'pending',
                'response' => json_encode($data)
            ]);
        }

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }
}
