<?php

namespace App\Http\Controllers;

use App\Classes\HdfcPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HdfcController extends Controller
{
    public function request(Request $request, HdfcPayment $service)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'production');
            })]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $redirectUrl = url('hdfc/callback');

        $response = $service->createSession($transaction->order_id, $transaction->amount, $transaction->reference_id, $redirectUrl);

        if (!$response['success']) {

            return response()->json($response);
        }

        return redirect($response['data']->paymentLinks["web"]);
    }

    public function callback(Request $request, HdfcPayment $service)
    {
        $data = $request->all();

        if (!isset($data['order_id'], $data['customer_id'])) {
            return response()->json('Invalid response');
        }

        $response = $service->orderStatus($data['order_id'], $data['customer_id']);

        if (!$response['success']) {
            return response()->json('Payment verification failed');
        }

        $order = $response['data'];

        $transaction = Transaction::where('order_id', $data['order_id'])->where('env', 'production')->first();

        if (!$transaction) {
            return response()->json('Transaction not found');
        }

        $status = strtolower($order->status);

        if ($status == 'charged') {
            $paymentStatus = 'completed';
        } elseif ($status == 'pending') {
            $paymentStatus = 'pending';
        } else {
            $paymentStatus = 'failed';
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => $paymentStatus,
            'response' => json_encode($order)
        ]);

        return redirect()->to('sandbox/redirect?reference_id=' . $transaction->reference_id);
    }
}
