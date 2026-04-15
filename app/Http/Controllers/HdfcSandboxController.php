<?php

namespace App\Http\Controllers;

use App\Classes\HdfcPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HdfcSandboxController extends Controller
{
    public function request(Request $request, HdfcPayment $service)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'sandbox');
            })]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $redirectUrl = url('hdfc/sandbox/callback');

        $response = $service->createSession($transaction->order_id, $transaction->amount, $transaction->reference_id, $redirectUrl);

        if (!$response['success']) {

            return response()->json($response);
        }

        return redirect($response['data']->paymentLinks["web"]);
    }

    public function callback(Request $request, HdfcPayment $service)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => ['required'],
            'razorpay_signature' => ['required'],
            'razorpay_order_id' => ['required'],
            'reference_id' => ['required', Rule::exists('transactions')->where('status', 'pending')->where('env', 'sandbox')]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $response = $service->orderStatus($transaction->order_id, $request->get('customer_id'));

        if (!$response['success']) {
            return response()->json($response);
        }

        $order = $response['data'];

        $update['request_response'] = json_encode($response);

        if ($order->status === "CHARGED") {

            $update['status'] = 'completed';
            $transaction->update($update);
        }

        if ($order->status === "PENDING") {

            $update['status'] = 'pending';
            $transaction->update($update);
        }

        $update['status'] = 'failed';

        $transaction->update($update);

        return redirect()->route('sandbox/redirect');
    }
}
