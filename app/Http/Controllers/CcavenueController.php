<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Traits\CcavenueTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CcavenueController extends Controller
{
    use CcavenueTrait;

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
            'redirect_url' => url('ccavenue/callback'),
            'cancel_url' => url('ccavenue/callback'),
            'language' => 'EN',
        ];

        foreach ($payload as $key => $value) {
            $merchantId .= $key . '=' . $value . '&';
        }

        $encryptedData = $this->encrypts($merchantId, $workingKey);

        return view('ccavenue.request', compact('encryptedData', 'accessCode', 'postUrl'));
    }

    public function callback(Request $request)
    {
        $encResponse = $request->get('encResp');

        if (!$encResponse) {
            return response()->json('Invalid request');
        }

        $workingKey = setting('ccavenue', 'working_key');

        $decryptedData = $this->decrypts($encResponse, $workingKey);

        parse_str($decryptedData, $data);

        if (!isset($data['order_id'], $data['order_status'])) {
            return redirect()->to(env('APP_URL'))->with('error', 'Invalid response');
        }

        $transaction = Transaction::where('order_id', $data['order_id'])->where('env', 'production')->first();

        if (!$transaction) {
            return redirect()->to(env('APP_URL'))->with('error', 'Transaction not found');
        }

        $status = strtolower($data['order_status']);

        $paymentStatus = ($status === 'success') ? 'success' : 'failed';

        $transaction->update([
            'status' => $paymentStatus,
            'response' => json_encode($data)
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
