<?php

namespace App\Http\Controllers;

use App\Classes\PaisaPay;
use App\Models\Payout;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $returnUrl = 'https://apexonline.in/payment/paisapay/callback?ref_id=' . $tnx->reference_id;

        // Generate and output the redirect form
        echo $sdk->createOrderForm($amount, $userPhone, $orderId, $returnUrl);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'ref_id' => 'required',
            'status' => 'required|in:success,failed,pending',
            // 'utr' => 'required'
        ]);

        $input = $request->all();

        $transaction = Transaction::where([
            'env' => 'production',
            'gateway' => 'paisapay',
            'reference_id' => $request->ref_id,
        ])->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->status == 'completed') {
            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        if ($request->status == 'success') {

            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($input),
                'payment_id' => $input['utr'] ?? $transaction->order_id
            ]);

            return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
        }

        $transaction->update([
            'status' => 'failed',
            'payment_response' => json_encode($input),
            'payment_id' => $input['utr'] ?? $transaction->order_id
        ]);

        return redirect()->to('redirect?reference_id=' . $transaction->reference_id);
    }

    public function payoutList(Request $request)
    {
        $payouts = Payout::when($request->filled('search'), function ($q) {
            $q->where('amount', 'like', 'like', "%" . request('search') . "%")
                ->orWhere('account_number', 'like', "%" . request('search') . "%")
                ->orWhere('ifsc_code', 'like', "%" . request('search') . "%")
                ->orWhere('holder_name', 'like', "%" . request('search') . "%")
                ->orWhere('bank_name', 'like', "%" . request('search') . "%");
        })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $total = Payout::count();
        $pendingTotal = Payout::where('status', 'pending')->count();
        $completedTotal = Payout::where('status', 'success')->count();
        $failedTotal = Payout::where('status', 'failed')->count();

        return view('admin.payout.list', compact(
            'payouts',
            'total',
            'pendingTotal',
            'completedTotal',
            'failedTotal'
        ));
    }

    public function payoutCreate()
    {
        return view('admin.payout.create');
    }

    public function payoutStore(Request $request)
    {
        $input = $request->validate([
            'amount' => 'required|numeric|min:2000',
            'account_number' => 'required|string|max:30',
            'confirm_account_number' => 'required|string|max:30|same:account_number',
            'ifsc_code' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        $input['order_id'] = 'PAYOUT_' . time();
        $input['uuid'] = str()->uuid();
        $input['status'] = 'pending';

        Payout::create($input);

        return redirect()
            ->route('payout.index')
            ->with('res.success', 'Payout request created successfully.');
    }


    public function payoutEdit(string $id)
    {
        $payout = Payout::where('status', 'pending')->findOrFail($id);

        return view('admin.payout.edit', compact('payout'));
    }

    public function payoutUpdate(Request $request, string $id)
    {
        $input = $request->validate([
            'amount' => 'required|numeric|min:2000',
            'account_number' => 'required|string|max:30',
            'confirm_account_number' => 'required|string|max:30|same:account_number',
            'ifsc_code' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        $payout = Payout::where('status', 'pending')->findOrFail($id);

        $payout->update($input);

        return redirect()
            ->route('payout.index')
            ->with('res.success', 'Payout request updated successfully.');
    }


    public function payoutSend(Request $request)
    {
        $validated = $request->validate([
            'payout_id' => 'required|integer|exists:payouts,id',
        ]);

        $payout = Payout::where('id', $validated['payout_id'])
            ->where('status', 'pending')
            ->firstOrFail();

        $url = 'https://pay.paisapay.site/create_payout.php';

        $postData = [
            'amount' => $payout->amount,
            'account_number' => $payout->account_number,
            'ifsc_code' => $payout->ifsc_code,
            'holder_name' => $payout->holder_name,
            'bank_name' => $payout->bank_name,
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Handle connection errors or timeouts.
        if ($response === false) {

            Log::error('Payout API cURL error', [
                'payout_id' => $payout->id,
                'error' => $curlError,
            ]);

            return back()->with(
                'res.error',
                'Unable to confirm payout status. Please verify with the provider before retrying.'
            );
        }

        $result = json_decode($response, true);

        // Handle invalid JSON or unsuccessful HTTP responses.
        if (!is_array($result) || $httpCode < 200 || $httpCode >= 300) {

            Log::error('Payout API error response', [
                'payout_id' => $payout->id,
                'http_code' => $httpCode,
                'response' => $response,
            ]);

            return back()->with(
                'res.error',
                'Invalid or unsuccessful payout API response. Please verify the transaction status.'
            );
        }

        // Confirm the provider's actual status values before mapping them.
        $apiStatus = strtolower((string) ($result['status'] ?? ''));

        if ($apiStatus === 'success') {

            $payout->update([
                'status' => 'success',
                'payment_response' => json_encode($result),
            ]);

            return back()->with('res.success', 'Payout processed successfully.');
        }

        if ($apiStatus === 'failed') {
            $payout->update([
                'status' => 'failed',
                'payment_response' => json_encode($result),
            ]);

            return back()->with('res.error', 'Payout failed.');
        }

        // Pending or unknown status: preserve the pending state.
        $payout->update([
            'payment_response' => json_encode($result),
        ]);

        return back()->with(
            'res.warning',
            'Payout status is pending or unconfirmed. Verify the transaction before retrying.'
        );
    }

    public function payoutStatus(Request $request)
    {
        file_put_contents(public_path('payout.json'), json_encode($request->all()));
    }
}
