<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function uaePayment(Request $request)
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

        $setting = Setting::whereIn('slug', [
            'bank_name',
            'account_no',
            'iban',
            'bank_holder_name'
        ])->pluck('value', 'slug');

        return view('general.uae_bank', compact('tnx', 'setting'));
    }

    public function uaePmtSubmit(Request $request)
    {
        $request->validate([
            'payment_slip' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['required', 'string', 'exists:transactions'],
            'tnx_id' => ['required', 'integer', 'exists:transactions,id']
        ]);

        $tnx = Transaction::where([
            'env' => 'production',
            'reference_id' => $request->reference_id,
            'id' => $request->tnx_id,
        ])->first();

        if (! $tnx) {

            return redirect()->back()->with(['error' => 'Transaction not found']);
        }

        $paymentSlip = uploadFile($request->payment_slip, 'payment_slip', $request->reference_id);

        $tnx->update([
            'payment_response' => json_encode([
                'slip' => $paymentSlip,
                'time' => now()->toDateTimeString(),
                'ip' => $request->ip(),
                'payment_id' => $tnx->payment_id,
            ]),
            'payment_id' => $request->payment_id,
            'status' => 'processing',
        ]);

        return redirect()->to('http://apexonline.in/pending.php?reference_id=' . $tnx->reference_id);
    }
}
