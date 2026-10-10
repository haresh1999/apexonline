<?php

// public function signatureGenerate(Request $request)
// {
//     $secret = '<YOUR SECRET>';

//     $payload = [
//         "order_id" => $request->order_id,
//         "payer_email" => $request->payer_email,
//         "payer_mobile" => $request->payer_mobile,
//         "payer_name" => $request->payer_name,
//         "refresh_token" => $request->refresh_token,
//     ];

//     ksort($payload);

//     $payloadQueryString = http_build_query($payload);

//     $calculatedSignature = hash_hmac('sha256', $payloadQueryString, $secret);

//     dd($calculatedSignature);
// }