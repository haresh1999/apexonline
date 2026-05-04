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
            return response()->json($validator->errors()->first());
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $data = [
            'key' => setting('payu', 'payu_key'),
            'txnid' => $transaction->order_id,
            'amount' => number_format($transaction->amount, 2, '.', ''),
            'productinfo' => "Order Payment {$transaction->id}",
            'firstname' => $transaction->payer_name,
            'email' => $transaction->payer_email,
            'phone' => $transaction->payer_mobile,
            'surl' => url('payu/sandbox/success'),
            'furl' => url('payu/sandbox/failed'),
            'udf1' => $transaction->reference_id
        ];

        $salt = setting('payu', 'payu_salt');

        $hash = $payu->generateHash($data, $salt);

        return view('payu.request', compact('data', 'hash'));
    }

    public function success(Request $request, PayuPayment $payu)
    {
        $input = $request->all();

        $salt = setting('payu', 'payu_salt');

        $isValidHash = $payu->verifyPayuResponse($input, $salt);

        if (!$isValidHash) {
            return response()->json('Invalid response');
        }

        $transaction = Transaction::where('reference_id', $input['udf1'])->first();

        if ($input['status'] === 'success') {

            $transaction->update([
                'request_response' => json_encode($input),
                'status' => 'completed',
            ]);
        } else {
            $transaction->update([
                'request_response' => json_encode($input),
                'status' => 'failed'
            ]);
        }

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }

    public function failed(Request $request, PayuPayment $payu)
    {
        $input = $request->all();

        $salt = setting('payu', 'payu_salt');

        $isValidHash = $payu->verifyPayuResponse($input, $salt);

        if (!$isValidHash) {
            return response()->json('Invalid response');
        }

        $transaction = Transaction::where('reference_id', $input['udf1'])->first();

        $transaction->update([
            'request_response' => json_encode($input),
            'status' => 'failed'
        ]);

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }
}
