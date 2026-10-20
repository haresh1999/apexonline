<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payout;

class PayoutController extends Controller
{
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
            // 'status' => ['required', 'in:0,1']
        ]);

        $input['order_id'] = 'PAYOUT_' . time();
        $input['uuid'] = str()->uuid();
        $input['status'] = 'pending';

        $payout = Payout::create($input);

        // if ($request->status == 1) {

        return $this->requestPayout($payout->id);
        // }

        // return redirect()
        //     ->route('payout.index')
        //     ->with('res.success', 'Payout request created successfully.');
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
            // 'status' => ['required', 'in:0,1']
        ]);

        $payout = Payout::where('status', 'pending')->findOrFail($id);

        $payout->update($input);

        // if ($request->status == 1) {

        return $this->requestPayout($payout->id);
        // }

        // return redirect()
        //     ->route('payout.index')
        // ->with('res.success', 'Payout request updated successfully.');
    }

    public function payoutSend(Request $request)
    {
        $validated = $request->validate([
            'payout_id' => 'required|integer|exists:payouts,id',
        ]);

        $payout_id = $validated['payout_id'];

        return $this->requestPayout($payout_id);
    }

    protected function requestPayout(string $payout_id)
    {
        // Claim the payout so concurrent requests cannot initiate it twice.
        $payout = DB::transaction(function () use ($payout_id) {
            $payout = Payout::where('id', $payout_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payout->status !== 'pending') {
                return null;
            }

            // Save a stable provider order ID before making the API call.
            if (empty($payout->order_id)) {
                $payout->order_id = 'PAYOUT_' . $payout->id;
                $payout->save();
            }

            return $payout;
        });

        if (!$payout) {
            return back()->with(
                'res.error',
                'This payout is not pending or has already been submitted.'
            );
        }

        $token = env('PP_MERCHANT_TOKEN');
        $secretKey = env('PP_MERCHANT_SECRET');
        $baseUrl = rtrim(env('PP_BASE_URL'), '/');

        $payloadData = [
            // 'order_id' => (string) $payout->order_id,
            'order_id' => 'PAYOUT_' . time(),
            'amount' => number_format((float) $payout->amount, 2, '.', ''),
            'account_number' => (string) $payout->account_number,
            'ifsc_code' => strtoupper((string) $payout->ifsc_code),
            'holder_name' => (string) $payout->holder_name,
            'bank_name' => (string) $payout->bank_name,
        ];

        $encrypted = openssl_encrypt(
            json_encode($payloadData),
            'aes-256-ecb',
            $secretKey,
            OPENSSL_RAW_DATA
        );

        if ($encrypted === false) {

            Log::error('PaisaPay payload encryption failed.', [
                'payout_id' => $payout->id,
            ]);

            return back()->with('res.error', 'Unable to encrypt payout request.');
        }

        $encryptedPayload = base64_encode($encrypted);

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->connectTimeout(10)
                ->timeout(30)
                ->post($baseUrl . '/create_payout.php', [
                    'token'   => $token,
                    'payload' => $encryptedPayload,
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('PaisaPay connection error; payout status is unknown.', [
                'payout_id' => $payout->id,
                'res.error' => $e->getMessage(),
            ]);

            // Do not automatically mark failed: the provider may have
            // received the request even though the connection timed out.
            return back()->with(
                'res.error',
                'Payout status could not be confirmed. Verify with PaisaPay before retrying.'
            );
        }

        $result = $response->json();

        if ($response->successful()) {

            $apiStatus = strtolower((string) ($result['status'] ?? ''));

            // Store the raw response for auditing.
            $payout->response = json_encode($result);

            if ($apiStatus === 'pending') {
                $payout->status = 'pending';
                $payout->save();

                return back()->with(
                    'res.success',
                    'Payout request queued successfully. Awaiting final confirmation.'
                );
            }

            if ($apiStatus === 'success') {
                $payout->status = 'success';
                $payout->save();

                return back()->with(
                    'res.success',
                    'Payout marked successful by the gateway.'
                );
            }

            if ($apiStatus === 'failed') {
                $payout->status = 'failed';
                $payout->save();

                return back()->with('res.error', 'Payout failed: ' .
                    ($result['message'] ?? 'Gateway rejected the request.'));
            }

            // Unknown response: do not assume success or failure.
            $payout->save();


            return back()->with(
                'res.error',
                $result['message']
            );
        }

        Log::error('PaisaPay returned an invalid or unsuccessful response.', [
            'payout_id' => $payout->id,
            'http_status' => $response->status(),
            'response' => $response->body(),
        ]);

        return back()->with(
            'res.warning',
            'Payout status is unconfirmed. Check the gateway before retrying.'
        );
    }

    public function payoutStatus(Request $request)
    {
        $request->validate([
            'payload' => ['required'],
            'token' => ['required']
        ]);

        $secretKey = env('PP_MERCHANT_SECRET');
        $payload = $request->payload;
        $payload = str_replace(' ', '+', $payload); // Ensure Base64 is preserved

        $decrypted = openssl_decrypt(base64_decode($payload), 'aes-256-ecb', $secretKey, OPENSSL_RAW_DATA);
        $callbackData = json_decode($decrypted, true);

        if ($callbackData) {

            if (strtolower($callbackData['status']) === 'success') {
                // Update user balance in your system
            }
            echo "success"; // Important: Acknowledge the webhook
        }

        echo "failed"; // Important: Acknowledge the webhook
    }
}
