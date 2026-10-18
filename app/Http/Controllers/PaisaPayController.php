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
        dd($request->all());

        return;
        // $transaction = Transaction::where([
        //     'env' => 'sandbox',
        //     'gateway' => 'hdfc',
        //     'order_id' => $data['order_id']
        // ])->first();

        // if (!$transaction) {
        //     return response()->json(['error' => 'Transaction not found'], 404);
        // }

        // if ($transaction->status == 'completed') {
        //     return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        // }

        // if (!$response['success']) {
        //     return response()->json('Payment verification failed');
        // }

        // $order = $response['data'];

        // $transaction->update([
        //     'status' => '<>',
        //     'payment_response' => '<>',
        //     'payment_id' => '<>',
        // ]);

        // return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
