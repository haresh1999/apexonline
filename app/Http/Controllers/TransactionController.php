<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function getToken()
    {
        $userId = config('services.user.id');

        $token = str()->uuid()->toString() . '-' . $userId;

        Token::create([
            'user_id' => $userId,
            'token' => $token,
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'refresh_token' => $token
        ]);
    }

    public function request(Request $request)
    {
        $user = config('services.user');
        $env = config('services.env');

        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                Rule::unique('transactions', 'order_id')->where(function ($query) use ($user, $env) {
                    return $query->where('user_id', $user['id'])
                        ->where('env', $env);
                }),
            ],
            'amount' => ['required', 'numeric', 'min:1', 'max:45000'],
            'payer_name' => ['required', 'string', 'max:255'],
            'payer_email' => ['required', 'email', 'max:255'],
            'payer_mobile' => ['required', 'digits:10'],
            'callback_url' => ['required', 'url'],
            'redirect_url' => ['required', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $validator->validated();

        $env = getAppEnv();

        if ($env == 'production') {

            $pgGateway = Transaction::where('status', 'completed')
                ->where('env', 'production')
                ->latest('id')
                ->value('gateway');

            $gateways = Gateway::where('status', 1)->pluck('slug')->toArray();

            $methods = [];

            foreach ($gateways as $key => $gateway) {
                $methods[$gateway] = $gateways[$key + 1] ?? $gateways[0];
            }

            $gateway = $methods[$pgGateway] ?? $gateways[array_rand($gateways)];

            // $gateway = 'hdfc';
            // $gateway = match ($pgGateway) {
            //     'hdfc' => 'instamojo',
            //     'instamojo' => 'cashfree',
            //     'cashfree' => 'phonepe',
            //     'phonepe' => 'payu',
            //     'payu' => 'paytm',
            //     'paytm' => 'sabpaisa',
            //     'sabpaisa' => 'zoho',
            //     'zoho' => 'hdfc',
            //     default => 'hdfc'
            // };
        } else {

            // $gateways = ['cashfree', 'phonepe', 'payu', 'sabpaisa'];

            // $gateway = $gateways[array_rand($gateways)];

            $gateway = 'upi';
        }

        $lastId = Transaction::latest('id')->value('id');

        $tnx = Transaction::create([
            'user_id' => $user['id'],
            'order_id' => 'WC_ORDER_' . ($lastId + 1),
            'mr_order_id' =>   $input['order_id'],
            'amount' => $input['amount'],
            'payer_name' => $input['payer_name'],
            'payer_email' => $input['payer_email'],
            'payer_mobile' => $input['payer_mobile'],
            'gateway' => $gateway,
            'callback_url' => $input['callback_url'],
            'redirect_url' => $input['redirect_url'],
            'gateway_id' => Gateway::where('slug', $gateway)->value('id')
        ]);

        if ($env == 'sandbox') {

            $url = "{$gateway}/{$env}/request?reference_id={$tnx->reference_id}&key=wc_order_{$tnx->id}";
        } else {

            $url = "{$gateway}/request?reference_id={$tnx->reference_id}&key=wc_order_{$tnx->id}";
        }

        return redirect()->to($url);
    }

    public function webhook($url, $secret, $data)
    {
        ksort($data);

        $payloadQueryString = http_build_query($data);

        $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);

        $response = Http::withHeaders([
            'X-Provider-Signature' => $calculatedSignature,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($url, $data);

        $tnx = Transaction::where('id', $data['transaction_id'])->first();

        if ($tnx->status == 'completed') {

            $data = [
                'declarant_name' => $tnx->payer_name,
                'email' => $tnx->payer_email,
                'phone' => $tnx->payer_mobile,
                'aadhaar_no' => '',
                'address' => '',
                'amount' => $tnx->amount,
                'payment_date' => Carbon::parse($tnx->created_at)->format('d / m / Y'),
                'payment_mode' => '',
                'payment_reference_no' => $tnx->payment_id ?? $tnx->mr_order_id,
            ];

            $pdf = Pdf::loadView('admin.declaration', compact('data'))->setPaper('a4', 'portrait');

            $path = storage_path('app/public/' . $tnx->reference_id . '.pdf');

            $pdf->save($path);

            $this->eSingRequest($tnx, $path);
        }

        return WebhookLog::create([
            'tnx_id'    => $tnx->id,
            'url'       => $url,
            'signature' => $calculatedSignature,
            'payload'   => json_encode($data),
            'response'  => $response->body(),
            'status'    => $response->status(),
            'user_id'   => $tnx?->user_id,
            'env'       => $tnx?->env,
        ]);
    }

    public function status(Request $request)
    {
        $user = config('services.user');

        $env = config('services.env');

        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'string',   Rule::exists('transactions', 'mr_order_id')->where(function ($query) use ($user, $env) {
                return $query->where('user_id', $user['id'])->where('env', $env);
            }),],
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $input = $validator->validated();

        $transaction = Transaction::where('user_id', $user['id'])
            ->where('mr_order_id', $input['order_id'])
            ->where('env', $env)
            ->first();

        $response['transaction_id'] = $transaction->id;
        $response['order_id'] = $transaction->mr_order_id;
        $response['reference_id'] = $transaction->reference_id;
        $response['amount'] = $transaction->amount;
        $response['refund_amount'] = $transaction->refund_amount;
        $response['status'] = $transaction->status;
        $response['payer_name'] = $transaction->payer_name;
        $response['payer_email'] = $transaction->payer_email;
        $response['payer_mobile'] = $transaction->payer_mobile;
        $response['redirect_url'] = $transaction->redirect_url;
        $response['callback_url'] = $transaction->callback_url;

        $signaturePayload = [
            'amount' => $response['amount'],
            'order_id' => $response['order_id'],
            'status' => $response['status']
        ];

        ksort($signaturePayload);

        $payloadQueryString = http_build_query($signaturePayload);

        $secret = $user['callback_secret'];

        $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);

        $response['signature'] = $calculatedSignature;

        return response()->json($response);
    }

    public function redirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions', 'reference_id')]
        ]);

        if ($validator->fails()) {

            return redirect()->to(env('APP_URL'));
        }

        $input = $validator->validated();

        $transaction = Transaction::where('reference_id', $input['reference_id'])->first();

        $callback_secret = User::where('id', $transaction->user_id)->value('callback_secret');

        $sendData = [
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->mr_order_id,
            'reference_id' => $transaction->reference_id,
            'amount' => $transaction->amount,
            'refund_amount' => $transaction->refund_amount,
            'status' => $transaction->status,
            'payer_name' => $transaction->payer_name,
            'payer_email' => $transaction->payer_email,
            'payer_mobile' => $transaction->payer_mobile,
            'redirect_url' => $transaction->redirect_url,
            'callback_url' => $transaction->callback_url,
        ];

        $callback_url = $transaction->callback_url;

        $this->webhook($callback_url, $callback_secret, $sendData);

        return redirect()->to($transaction->redirect_url . '?status=' . $transaction->status);
    }

    public function signatureGenerate(Request $request)
    {
        $secret = '17a89db4-4096-4d02-a3af-29ba3f259096';

        $payload = [
            "order_id" => $request->order_id,
            "payer_email" => $request->payer_email,
            "payer_mobile" => $request->payer_mobile,
            "payer_name" => $request->payer_name,
            "refresh_token" => $request->refresh_token,
        ];

        ksort($payload);

        $payloadQueryString = http_build_query($payload);

        $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);

        dd($calculatedSignature);
    }

    public function eSingRequest($tnx, $pdfPath)
    {
        $response = Http::withHeaders([
            'X-API-KEY' => '2CZkOiKoWZt27ssskNZmmKUvIscxtctK',
            'X-API-APP-ID' => '18166fa6-1c0d-4925-a16e-330fdef087ca'
        ])
            ->post('https://uat-ext.signcare.io/api/v1/eSign/request', [
                'referenceId' => $tnx->reference_id,
                // 'referenceId' => \Str::random(10),
                'skipVerificationCode' => false,
                'documentInfo' => [
                    'name' => 'service-completion.pdf',
                    'content' => pdfToBase64($pdfPath),
                ],
                'supportingDocuments' => [],
                'sequentialSigning' => true,
                'userInfo' => [
                    [
                        'name' => $tnx->payer_name,
                        'emailId' => $tnx->payer_email,
                        // 'emailId' => 'hareshc1999@gmail.com',
                        'userType' => 'Signer',
                        'signatureType' => 'Electronic',

                        'electronicOptions' => [
                            'canDraw' => true,
                            'canType' => false,
                            'canUpload' => false,
                            'captureGPSLocation' => false,
                            'capturePhoto' => false,
                        ],

                        'aadhaarInfo' => null,
                        'aadhaarOptions' => null,
                        'signatureExpiryDate' => null,
                        'emailReminderDays' => null,

                        'mobileNo' => '',
                        'order' => 1,
                        'userReferenceId' => $tnx->mr_order_id,
                        'signAppearance' => 5,
                        'pageToBeSigned' => 1,
                        'pageNumber' => null,

                        'pageCoordinates' => [
                            [
                                'pageNumber' => 1,
                                'pageSize' => 841.89,
                                'pageWidth' => 595.28,

                                'pdfCoordinates' => [
                                    [
                                        'x1' => 45.76,
                                        'y1' => 639.19,
                                        'x2' => 120,
                                        'y2' => 40,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                'descriptionForInvitee' => 'eSign By APEX',
                'finalCopyRecipientsEmailId' => '',
                'responseUrl' => route('esign.wh', $tnx->reference_id),
                'returnUrl' => route('esign.agree', $tnx->reference_id),
                'uiMode' => false,
            ]);

        if ($response->successful()) {

            $result = $response->json();

            return $tnx->update([
                'esign_id' => $result['data']['documentId'] ?? null,
                'esign_status' => 'pending'
            ]);
        }

        return $tnx->update([
            'esign_status' => 'try'
        ]);
    }

    public function esignWebhook(Request $request, string $refId)
    {
        if ($request->DocumentStatus == 'Signed') {

            try {

                base64ToPdf($request->Content, storage_path('app/public/' . $refId . '.pdf'));

                Transaction::where('reference_id', $refId)->update(['esign_status' => 'completed']);
            } catch (\Throwable $th) {

                logger($th->getMessage());

                return 'failed';
            }
        }

        return 'ok';
    }

    public function esignAgree(string $refId)
    {
        $tnx = Transaction::where('reference_id', $refId)->firstOrFail();

        $filePath = storage_path('app/public/' . $tnx->reference_id . '.pdf');

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="apexonline-service-completion.pdf"',
        ]);
    }
}
