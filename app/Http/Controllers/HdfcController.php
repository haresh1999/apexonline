<?php

namespace App\Http\Controllers;

use App\Classes\HdfcService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HdfcController extends Controller
{
    public function request(Request $request, HdfcService $hdfcService)
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

        $response = $hdfcService->createOrderSession([
            'order_id'       => $transaction->order_id,
            'amount'         => $transaction->amount,
            'customer_id'    => 'CUST_' . $transaction->id,
            'customer_email' => $transaction->payer_email,
            'customer_phone' => $transaction->payer_mobile,
            'return_url'     => url('hdfc/callback'),
            'description'    => 'WOOCOMMERCE ORDER PAYMENT OF ORDER ID ' . $transaction->orderId,
            'first_name'     => $transaction->payer_name,
        ]);

        if (!$response['success']) {
            return response()->json([
                'message' => 'Payment session failed',
                'response' => $response
            ], 500);
        }

        $paymentUrl = $response['data']['payment_links']['web'] ?? $response['data']['paymentLinks']['web'] ?? null;

        if (!$paymentUrl) {
            return response()->json([
                'message' => 'Payment URL not found',
                'response' => $response
            ], 500);
        }

        return redirect()->away($paymentUrl);
    }

    public function callback(Request $request, HdfcService $hdfcService)
    {
        $data = $request->all();

        if (!isset($data['order_id'])) {
            return response()->json('Invalid response');
        }

        $transaction = Transaction::where([
            'env' => 'production',
            'gateway' => 'hdfc',
            'order_id' => $data['order_id']
        ])->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $response = $hdfcService->orderStatus($transaction->order_id);

        if (!$response['success']) {
            return response()->json('Payment verification failed');
        }

        $order = $response['data'];

        $status = strtolower($order->status);

        if ($status == 'charged') {
            $paymentStatus = 'completed';
        } elseif ($status == 'pending') {
            $paymentStatus = 'pending';
        } else {
            $paymentStatus = 'failed';
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => $paymentStatus,
            'response' => json_encode($order)
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }
}
