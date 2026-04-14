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
            return redirect()->to(env('APP_URL'));
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

    public function callback(Request $request)
    {
        dd($request->all());
    }
}
