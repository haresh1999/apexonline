<?php

namespace App\Http\Controllers;

use App\Classes\PaisaPay;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaisaPayController extends Controller
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

        $tnx = Transaction::where('reference_id', $request->reference_id)->first();

        // Configure your API details here

        $sdk = new PaisaPay(
            env('PP_BASE_URL'),
            env('PP_MERCHANT_TOKEN'),
            env('PP_MERCHANT_SECRET'),
        );

        // Payment details
        $amount = $tnx->amount;
        $userPhone = $tnx->payer_mobile;
        $orderId = $tnx->order_id;
        $returnUrl = 'https://apexonline.in/payment/paisapay/callback?ref_id=' . $tnx->reference_id;

        // Generate and output the redirect form
        echo $sdk->createOrderForm($amount, $userPhone, $orderId, $returnUrl);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => 'required',
            'status' => 'required|in:success,failed,pending',
            'utr' => 'required',
            'payload' => 'required',
            'order_id' => 'required',
            'amount' => 'required'
        ]);

        $input = $request->all();

        $transaction = Transaction::where([
            'env' => 'production',
            'gateway' => 'paisapay',
            'reference_id' => $request->ref_id,
        ])->first();

        if (!$transaction) {
            return response()->json(['res.error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $secretKey = env('PP_MERCHANT_SECRET');
        $payload = $request->payload;
        $payload = str_replace(' ', '+', $payload); // Ensure Base64 is preserved

        $decrypted = openssl_decrypt(base64_decode($payload), 'aes-256-ecb', $secretKey, OPENSSL_RAW_DATA);
        $response = json_decode($decrypted, true);

        $status = strtolower($input['status']);

        if ($status == 'success') {

            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($response),
                'payment_id' => $input['utr'] ?? $transaction->order_id
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => 'failed',
            'payment_response' => json_encode($response),
            'payment_id' => $input['utr'] ?? $transaction->order_id
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
