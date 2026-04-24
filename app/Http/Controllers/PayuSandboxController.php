<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Classes\PayuPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayuSandboxController extends Controller
{
    public function request(Request $request, PayuPayment $payu)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'sandbox');
            })]
        ]);

        if ($validator->fails()) {
            return redirect()->to(env('APP_URL'));
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $data = [
            'key' => setting('payu', 'payu_key'),
            'txnid' => $transaction->order_id,
            'amount' => number_format($transaction->amount, 2, '.', ''),
            'productinfo' => "#Order Id:{$transaction->id} Payment",
            'firstname' => $transaction->payer_name,
            'email' => $transaction->payer_email,
            'phone' => $transaction->payer_mobile,
            'surl' => url('payu.sandbox.success'),
            'furl' => url('payu.sandbox.failed'),
        ];

        $hash = $payu->generateHash($data);

        return view('payu', compact('data', 'hash'));
    }

    public function success(Request $request)
    {
        $request;
    }

    public function failed(Request $request)
    {
        $request;
    }
}
