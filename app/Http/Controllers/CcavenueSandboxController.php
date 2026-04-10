<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Traits\CcavenueTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CcavenueSandboxController extends Controller
{
    use CcavenueTrait;

    public function request(Request $request)
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

        $merchantId = setting('ccavenue', 'merchant_id');
        $workingKey = setting('ccavenue', 'working_key');
        $accessCode = setting('ccavenue', 'access_code');
        $postUrl = setting('ccavenue', 'post_url');

        $payload = [
            'name' => $transaction->payer_name,
            'email' => $transaction->payer_email,
            'merchant_id' => $merchantId,
            'order_id' => $transaction->order_id,
            'currency' => 'INR',
            'amount' => $transaction->amount,
            'redirect_url' => url('ccavenue/sandbox/callback'),
            'cancel_url' => url('ccavenue/sandbox/callback'),
            'language' => 'EN',
        ];

        foreach ($payload as $key => $value) {
            $merchantId .= $key . '=' . $value . '&';
        }

        $encryptedData = $this->encrypts($merchantId, $workingKey);

        return view('ccavenue.request', compact('encryptedData', 'accessCode', 'postUrl'));
    }
}
