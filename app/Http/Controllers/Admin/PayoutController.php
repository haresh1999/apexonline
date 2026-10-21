<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payout;

class PayoutController extends Controller
{
    public function list(Request $request)
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

    public function create(BankInfo $bankInfo)
    {
        $banks = [];

        if (! $bankInfo->account_number) {

            $banks = BankInfo::all();
        }

        return view('admin.payout.create', compact('bankInfo', 'banks'));
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'amount' => 'required|numeric|min:2000',
        ]);

        $bankInfo = BankInfo::where('id', $request->bank_id)->first();

        $input['order_id'] = 'payout_' . (bin2hex(random_bytes(8)));
        $input['uuid'] = str()->uuid();
        $input['status'] = 'pending';
        $input['account_number'] = $bankInfo->account_number;
        $input['ifsc_code'] = $bankInfo->ifsc_code;
        $input['holder_name'] = $bankInfo->holder_name;
        $input['bank_name'] = $bankInfo->bank_name;

        $res = $this->requestPayout($input);

        return redirect()->route('payout.index')->with($res['name'], $res['message']);
    }

    protected function requestPayout(array $payoutData)
    {
        $payout = Payout::create($payoutData);

        $token = env('PP_MERCHANT_TOKEN');
        $secretKey = env('PP_MERCHANT_SECRET');
        $baseUrl = rtrim(env('PP_BASE_URL'), '/');

        $payloadData = [
            'order_id' => (string) $payout->order_id,
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

            return [
                'name' => 'res.error',
                'message' => 'Unable to encrypt payout request.'
            ];
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
            return [
                'name' => 'res.error',
                'message' => 'Payout status could not be confirmed. Verify with PaisaPay before retrying.'
            ];
        }

        $result = $response->json();

        if ($response->successful()) {

            $apiStatus = strtolower((string) ($result['status'] ?? ''));

            // Store the raw response for auditing.
            $payout->response = json_encode($result);

            if ($apiStatus === 'pending') {
                $payout->status = 'pending';
                $payout->save();

                return [
                    'name' => 'res.success',
                    'message' => 'Payout request queued successfully. Awaiting final confirmation.'
                ];
            }

            if ($apiStatus === 'success') {
                $payout->status = 'success';
                $payout->save();

                return [
                    'name' => 'res.success',
                    'message' => 'Payout marked successful by the gateway.'
                ];
            }

            if ($apiStatus === 'failed') {
                $payout->status = 'failed';
                $payout->save();

                return [
                    'name' => 'res.error',
                    'message' => 'Payout failed: ' . ($result['message'] ?? 'Gateway rejected the request.')
                ];
            }

            // Unknown response: do not assume success or failure.
            $payout->save();

            return [
                'name' => 'res.error',
                'message' => $result['message']
            ];
        }

        Log::error('PaisaPay returned an invalid or unsuccessful response.', [
            'payout_id' => $payout->id,
            'http_status' => $response->status(),
            'response' => $response->body(),
        ]);

        return [
            'name' => 'res.warning',
            'message' => 'Payout status is unconfirmed. Check the gateway before retrying.'
        ];
    }

    public function status(Request $request)
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

        $status = strtolower($callbackData['status']);

        $payout = Payout::where('order_id', $callbackData['order_id'])->first();

        if ($payout && $callbackData) {

            $payout->update([
                'status' => $status,
                'response' => $callbackData
            ]);
        }

        echo $status;
    }
}
