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

        // Generate and output the redirect form
        echo $sdk->createOrderForm($amount, $userPhone, $orderId);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'udf1' => 'required',
            'status' => 'required|in:SUCCESS,FAILED,PENDING',
            'amount' => 'required|numeric',
            'utr' => 'required'
        ]);

        $input = $request->all();

        $transaction = Transaction::where([
            'env' => 'production',
            'gateway' => 'paisapay',
            'order_id' => $request->udf1
        ])->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        if ($input['status'] === 'SUCCESS') {

            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($input),
                'payment_id' => $input['utr'] ?? null
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => 'failed',
            'payment_response' => json_encode($input),
            'payment_id' => $input['utr'] ?? null
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
