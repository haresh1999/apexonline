<?php

namespace App\Http\Controllers;

use App\Mail\CourseMail;
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
use Illuminate\Support\Facades\Mail;

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

            if (is_null($user['default_gateway'])) {

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
            } else {

                $gateway = strtolower($user['default_gateway']);
            }
        } else {

            if (is_null($user['default_gateway'])) {

                $gateways = ['cashfree', 'phonepe', 'payu', 'sabpaisa'];

                $gateway = $gateways[array_rand($gateways)];
            } else {
                $gateway = strtolower($user['default_gateway']);
            }
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

    public function webhook(string $url, string $secret, array $data)
    {
        ksort($data);

        $payloadQueryString = http_build_query($data);

        $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);

        $response = Http::withHeaders([
            'X-Provider-Signature' => $calculatedSignature,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($url, $data);

        $tnx = Transaction::where('id', $data['transaction_id'])->first();

        if ($tnx->status == 'completed' && ! in_array($tnx->user->client_id, ['apexonline', 'apexonline_web'])) {

            $pdfPath = $this->sendCourseMail($tnx);

            $this->eSingRequest($tnx, $pdfPath);
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

    public function sendCourseMail(object $tnx)
    {
        $course = getCourse((float) $tnx->amount); //  GET COURSE

        // GENERATE DECLARATION PDF

        $declarationData = [
            'declarant_name' => $tnx->payer_name,
            'email' => $tnx->payer_email,
            'phone' => $tnx->payer_mobile,
            'aadhaar_no' => '',
            'address' => '',
            'amount' => $tnx->amount,
            'payment_date' => Carbon::parse($tnx->created_at)->format('d / m / Y'),
            'payment_reference_no' => $tnx->payment_id ?? $tnx->mr_order_id,
            'course' => $course['name'],
        ];

        $declarationDir = storage_path('app/public/declaration');

        if (!is_dir($declarationDir)) {
            mkdir($declarationDir, 0755, true);
        }

        $declarationPath = $declarationDir . '/' . ids($tnx->id) . '.pdf';

        $declarationPdf = Pdf::loadView('admin.declaration', ['data' => $declarationData])->setPaper('a4', 'portrait');

        $declarationPdf->save($declarationPath);

        // GENERATE INVOICE PDF

        $invoiceData = [
            'invoice_no' => ($tnx->id + 2763),
            'date' => Carbon::parse($tnx->created_at)->format('d-m-Y'),
            'customer_name' => $tnx->payer_name,
            'email' => $tnx->payer_email,
            'mobile' => '+91 ' . $tnx->payer_mobile,
            'item_name' => $course['name'],
            'quantity' => 1,
            'amount' => $tnx->amount,
            'utr' => $tnx->payment_id,
        ];

        $invoiceDir = storage_path('app/public/invoice');

        if (!is_dir($invoiceDir)) {
            mkdir($invoiceDir, 0755, true);
        }

        $invoicePath = $invoiceDir . '/' . ids($tnx->id) . '.pdf';

        $invoicePdf = Pdf::loadView('invoice', ['data' => $invoiceData])->setPaper('a4', 'portrait');

        $invoicePdf->save($invoicePath);

        //  SEND COURSE + INVOICE EMAIL

        Mail::to($tnx->payer_email)->bcc([
            'haresh@swapinfoway.com',
            'apexonlinein@gmail.com'
        ])->send(
            new CourseMail(
                $invoicePath,
                $declarationPath,
                $course['path'],
                $tnx->payer_name,
                $course['name'],
                $tnx->order_id,
                $tnx->amount,
                Carbon::parse($tnx->created_at)->format('d-m-Y'),
                $course['url'],
                ids($tnx->id),
                $course['subject'],
            )
        );

        return $declarationPath;
    }

    public function eSingRequest(object $tnx, string $pdfPath)
    {
        $response = Http::withHeaders([
            'X-API-KEY' => env('ESIGN_X_API_KEY'),
            'X-API-APP-ID' => env('ESIGN_X_API_APP_ID')
        ])
            ->post('https://ext.signcare.io/api/v1/eSign/request', [
                'referenceId' => $tnx->reference_id,
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

                base64ToPdf($request->Content, storage_path('app/public/declaration/' . $refId . '.pdf'));

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

        $filePath = public_path('storage/declaration/' . $tnx->reference_id . '.pdf');

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="apexonline-service-completion.pdf"',
        ]);
    }
}
