<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Traits\UpiPaymentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpiSandboxController extends Controller
{
    use UpiPaymentTrait;

    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => ['required', Rule::exists('transactions')->where(function ($q) {
                $q->where('status', 'pending')->where('env', 'sandbox');
            })]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first());
        }

        $tnx = Transaction::where('reference_id', $request->reference_id)->first();

        $customerName  = $tnx->payer_name;
        $customerEmail = $tnx->payer_email;
        $customerMobile = $tnx->payer_mobile;
        $amount = $tnx->amount;
        $orderNo = $tnx->order_id;

        $txn_details = implode('|', [
            env('UPI_SBX_AGGREGATOR_ID'),
            env('UPI_SBX_MERCHANT_ID'),
            $orderNo,
            $amount,
            'IND',
            'INR',
            'SALE',
            url('upi/sandbox/callback', $tnx->reference_id),
            url('upi/sandbox/callback', $tnx->reference_id),
            'WEB'
        ]);

        // ---------------------------------------
        // PG DETAILS
        // Aggregator Hosted Non-Seamless
        // Keep blank
        // ---------------------------------------

        $pg_details = implode('|', [
            '',
            '',
            '',
            ''
        ]);

        // ---------------------------------------
        // CARD DETAILS
        // Must be blank
        // ---------------------------------------

        $card_details = implode('|', [
            '',
            '',
            '',
            '',
            ''
        ]);

        // ---------------------------------------
        // CUSTOMER DETAILS
        // ---------------------------------------

        $cust_details = implode('|', [
            $customerName,
            $customerEmail,
            $customerMobile,
            '', // unique_id
            'N' // is_logged_in
        ]);

        // ---------------------------------------
        // BILLING DETAILS
        // ---------------------------------------

        $bill_details = implode('|', [
            '',
            '',
            '',
            '',
            ''
        ]);

        // ---------------------------------------
        // SHIPPING DETAILS
        // ---------------------------------------

        $ship_details = implode('|', [
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        // ---------------------------------------
        // ITEM DETAILS
        // ---------------------------------------

        $item_details = implode('|', [
            '1',
            $amount,
            'Service'
        ]);

        // ---------------------------------------
        // UPI DETAILS
        // ---------------------------------------

        $upi_details = implode('|', [
            ''
        ]);

        // ---------------------------------------
        // OTHER DETAILS
        // udf_1 to udf_6
        // ---------------------------------------

        $other_details = implode('|', [
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        // ---------------------------------------
        // FINAL REQUEST
        // ---------------------------------------

        $all_values =
            $txn_details . '~' .
            $pg_details . '~' .
            $card_details . '~' .
            $cust_details . '~' .
            $bill_details . '~' .
            $ship_details . '~' .
            $item_details . '~' .
            $upi_details . '~' .
            $other_details;


        // ---------------------------------------
        // Encrypt merchant_request
        // ---------------------------------------

        $merchant_request = $this->encryptTouras(
            $all_values,
            env('UPI_SBX_ENCRYPTION_KEY')
        );

        // ---------------------------------------
        // Generate Hash
        // ---------------------------------------

        $hash = $this->createTourasHash(
            env('UPI_SBX_MERCHANT_ID'),
            $orderNo,
            $amount,
            'IND',
            'INR',
            env('UPI_SBX_ENCRYPTION_KEY')
        );

        $paymentUrl = 'https://uatcheckout.touras.in/ms-transaction-core/paymentRedirection/checksumGatewayPage';
        $merchantId = env('UPI_SBX_MERCHANT_ID');

        return view('upi.request', compact('hash', 'paymentUrl', 'merchantId', 'merchant_request', 'hash'));
    }

    public function callback(Request $request, string $refId)
    {
        $tnx = Transaction::where('reference_id', $refId)
            ->where('gateway', 'upi')
            ->where('env', 'sandbox')
            ->first();

        if (empty($_POST['txn_response']) || !$tnx) {

            return response()->json([
                'error' => 'Transaction not found.'
            ], 404);
        }

        // ALREADY PROCESSED
        if ($tnx->status == 'completed') {
            return redirect()->to('sandbox/redirect?reference_id=' . $tnx->reference_id);
        }

        $encryptedResponse = $request->txn_response;

        $decryptedResponse = $this->decryptTouras(
            $encryptedResponse,
            env('UPI_SBX_ENCRYPTION_KEY')
        );

        if ($decryptedResponse === false) {

            return response()->json([
                'error' => 'Unable to decrypt transaction response.'
            ], 404);
        }

        $data = explode('|', $decryptedResponse);

        $result['agId'] = $data[0] ?? '';
        $result['merchantId'] = $data[1] ?? '';
        $result['orderNo'] = $data[2] ?? '';
        $result['amount'] = $data[3] ?? '';
        $result['country'] = $data[4] ?? '';
        $result['currency'] = $data[5] ?? '';
        $result['txnDate'] = $data[6] ?? '';
        $result['txnTime'] = $data[7] ?? '';
        $result['agRef'] = $data[8] ?? '';
        $result['pgRef'] = $data[9] ?? '';
        $result['status'] = $data[10] ?? '';
        $result['responseCode'] = $data[11] ?? '';
        $result['responseMsg'] = $data[12] ?? '';

        if ($result['merchantId'] !== env('UPI_SBX_MERCHANT_ID')) {

            return response()->json([
                'error' => 'Merchant ID mismatch.'
            ], 404);
        }

        if (strtolower($result['status']) === 'successful') {

            $tnx->update([
                'status' => 'completed',
                'payment_response' => json_encode($result),
                'payment_id' => $result['agId']
            ]);

            return redirect()->to('sandbox/redirect?reference_id=' . $tnx->reference_id);
        }

        $tnx->update([
            'status' => 'failed',
            'payment_response' => json_encode($result),
            'payment_id' => $result['agId']
        ]);

        return redirect()->to('sandbox/redirect?reference_id=' . $tnx->reference_id);
    }
}
