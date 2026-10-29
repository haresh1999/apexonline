<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PayoutExport;
use App\Exports\PayoutImport;
use App\Http\Controllers\Controller;
use App\Models\BankInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payout;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayoutController extends Controller
{
    public function list(Request $request)
    {
        $payouts = Payout::with('user')
            ->when($request->filled('search'), function ($q) {
                $q->where('amount', 'like', 'like', "%" . request('search') . "%")
                    ->orWhere('account_number', 'like', "%" . request('search') . "%")
                    ->orWhere('ifsc_code', 'like', "%" . request('search') . "%")
                    ->orWhere('holder_name', 'like', "%" . request('search') . "%")
                    ->orWhere('bank_name', 'like', "%" . request('search') . "%");
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->authUser()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $total = Payout::authUser()->count();
        $pendingTotal = Payout::authUser()->where('status', 'pending')->count();
        $completedTotal = Payout::authUser()->where('status', 'success')->count();
        $failedTotal = Payout::authUser()->where('status', 'failed')->count();

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

            $banks = BankInfo::authUser()->get();
        }

        return view('admin.payout.create', compact('bankInfo', 'banks'));
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'amount' => 'required|numeric|min:2000',
            'bank_id' => ['required', 'integer', Rule::exists('bank_infos', 'id')->where('user_id', getUserId())]
        ]);

        $bankInfo = BankInfo::authUser()->where('id', $request->bank_id)->firstOrFail();

        $input['account_number'] = $bankInfo->account_number;
        $input['ifsc_code'] = $bankInfo->ifsc_code;
        $input['holder_name'] = $bankInfo->holder_name;
        $input['bank_name'] = $bankInfo->bank_name;
        $input['order_id'] = 'payout_' . (bin2hex(random_bytes(8)));
        $input['uuid'] = str()->uuid();
        $input['status'] = 'pending';
        $input['user_id'] = getUserId();

        Payout::create($input);

        return redirect()->route('payout.index')->with('res.success', 'Payout created successfully.');
    }

    public function send(Request $request)
    {
        $request->validate([
            'payout_id' => ['required', 'integer', 'exists:payouts,id']
        ]);

        $payout = Payout::authUser()->find($request->payout_id);

        $res = $this->requestPayout($payout);

        return redirect()->route('payout.index')->with($res['name'], $res['message']);
    }

    public function requestPayout(object $payout)
    {
        if ($payout->response) {

            $signatureData = [
                'amount' => $payout->amount,
                'account_number' => $payout->account_number,
                'ifsc_code' => $payout->ifsc_code,
                'holder_name' => $payout->holder_name,
                'bank_name' => $payout->bank_name,
            ];

            ksort($signatureData);
            $payloadQueryString = http_build_query($signatureData);
            $secret = $payout->user->callback_secret;
            $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);
            $response = json_decode($payout->response);
            $callback_url = $response->callback_url;

            $data = [
                'amount' => $payout->amount,
                'account_number' => $payout->account_number,
                'ifsc_code' => $payout->ifsc_code,
                'holder_name' => $payout->holder_name,
                'bank_name' => $payout->bank_name,
                'created_at' => Carbon::parse($payout->created_at)->format('Y-m-d h:i A'),
                'updated_at' => Carbon::parse($payout->updated_at)->format('Y-m-d h:i A'),
                'status' => $payout->status,
                'order_id' => $payout->order_id,
                'uuid' => $payout->uuid
            ];

            $response = Http::withHeaders([
                'X-Provider-Signature' => $calculatedSignature,
                'Content-Type' => 'application/json'
            ])->post($callback_url, $data);
        }

        $payout->status = 'success';
        $payout->save();

        return [
            'name' => 'res.success',
            'message' => 'Payout status updated successfully.'
        ];

        // $token = env('PP_MERCHANT_TOKEN');
        // $secretKey = env('PP_MERCHANT_SECRET');
        // $baseUrl = rtrim(env('PP_BASE_URL'), '/');

        // $payloadData = [
        //     'order_id' => (string) $payout->order_id,
        //     'amount' => number_format((float) $payout->amount, 2, '.', ''),
        //     'account_number' => (string) $payout->account_number,
        //     'ifsc_code' => strtoupper((string) $payout->ifsc_code),
        //     'holder_name' => (string) $payout->holder_name,
        //     'bank_name' => (string) $payout->bank_name,
        // ];

        // $encrypted = openssl_encrypt(
        //     json_encode($payloadData),
        //     'aes-256-ecb',
        //     $secretKey,
        //     OPENSSL_RAW_DATA
        // );

        // if ($encrypted === false) {

        //     Log::error('PaisaPay payload encryption failed.', [
        //         'payout_id' => $payout->id,
        //     ]);

        //     return [
        //         'name' => 'res.error',
        //         'message' => 'Unable to encrypt payout request.'
        //     ];
        // }

        // $encryptedPayload = base64_encode($encrypted);

        // try {
        //     $response = Http::asForm()
        //         ->acceptJson()
        //         ->connectTimeout(10)
        //         ->timeout(30)
        //         ->post($baseUrl . '/create_payout.php', [
        //             'token'   => $token,
        //             'payload' => $encryptedPayload,
        //         ]);
        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
        //     Log::warning('PaisaPay connection error; payout status is unknown.', [
        //         'payout_id' => $payout->id,
        //         'res.error' => $e->getMessage(),
        //     ]);

        //     // Do not automatically mark failed: the provider may have
        //     // received the request even though the connection timed out.
        //     return [
        //         'name' => 'res.error',
        //         'message' => 'Payout status could not be confirmed. Verify with PaisaPay before retrying.'
        //     ];
        // }

        // $result = $response->json();

        // if ($response->successful()) {

        //     $apiStatus = strtolower((string) ($result['status'] ?? ''));

        //     // Store the raw response for auditing.
        //     $payout->response = json_encode($result);

        //     if ($apiStatus === 'pending') {
        //         $payout->status = 'pending';
        //         $payout->save();

        //         return [
        //             'name' => 'res.success',
        //             'message' => 'Payout request queued successfully. Awaiting final confirmation.'
        //         ];
        //     }

        //     if ($apiStatus === 'success') {
        //         $payout->status = 'success';
        //         $payout->save();

        //         return [
        //             'name' => 'res.success',
        //             'message' => 'Payout marked successful by the gateway.'
        //         ];
        //     }

        //     if ($apiStatus === 'failed') {
        //         $payout->status = 'failed';
        //         $payout->save();

        //         return [
        //             'name' => 'res.error',
        //             'message' => 'Payout failed: ' . ($result['message'] ?? 'Gateway rejected the request.')
        //         ];
        //     }

        //     // Unknown response: do not assume success or failure.
        //     $payout->save();

        //     return [
        //         'name' => 'res.error',
        //         'message' => $result['message']
        //     ];
        // }

        // Log::error('PaisaPay returned an invalid or unsuccessful response.', [
        //     'payout_id' => $payout->id,
        //     'http_status' => $response->status(),
        //     'response' => $response->body(),
        // ]);

        // return [
        //     'name' => 'res.warning',
        //     'message' => 'Payout status is unconfirmed. Check the gateway before retrying.'
        // ];
    }

    public function status(Request $request)
    {
        Log::info('PaisaPay Payout Response.', [
            'response' => json_encode($request->all()),
        ]);

        $request->validate([
            'payload' => ['required'],
            'token' => ['required']
        ]);

        try {

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

            Log::info('PaisaPay Payout Success.', [
                'response' => json_encode($request->all()),
            ]);

            echo $status;
        } catch (\Throwable $th) {

            Log::error('PaisaPay Payout Error.', [
                'message' => $th->getMessage(),
                'response' => json_encode($request->all()),
            ]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {

            Excel::import(new PayoutImport(), $request->file);

            return redirect()->route('payout.index')->with('res.success', 'Payouts imported successfully.');
        } catch (\Throwable $th) {

            return redirect()->route('payout.index')->with('res.error', 'Failed to import payouts. Please check the file format and try again.');
        }
    }

    public function export()
    {
        return Excel::download(new PayoutExport(), 'payout.xlsx');
    }

    public function slip(Payout $payout)
    {
        $pdf = Pdf::loadView('admin.payout.slip', compact('payout'))->setPaper([0, 0, 255, 485], 'portrait');

        return $pdf->stream('receipt.pdf');

        // return view('admin.payout.slip');
    }

    public function apiCreate(Request $request)
    {
        $user = config('services.user');

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:2000'],
            'bank_id' => ['required', 'integer', Rule::exists('bank_infos', 'id')->where('user_id', $user['id'])],
            'callback_url' => ['required', 'url'],
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => $validator->errors(),
                'status' => false
            ], 404);
        }

        $input = $validator->validate();

        $bankInfo = BankInfo::where('user_id', $user['id'])
            ->where('id', $input['bank_id'])
            ->first();

        $input['account_number'] = $bankInfo->account_number;
        $input['ifsc_code'] = $bankInfo->ifsc_code;
        $input['holder_name'] = $bankInfo->holder_name;
        $input['bank_name'] = $bankInfo->bank_name;
        $input['order_id'] = 'payout_' . (bin2hex(random_bytes(8)));
        $input['uuid'] = str()->uuid();
        $input['status'] = 'pending';
        $input['user_id'] = $user['id'];
        $input['response'] = json_encode([
            'callback_url' => $request->callback_url
        ]);

        Payout::create($input);

        return response()->json([
            'message' => 'Payout request send successfully.',
            'status' => true
        ], 200);
    }
}
