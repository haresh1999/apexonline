<?php

return [
    "merchant_id" => env("HDFC_MERCHANT_ID"),
    "key_uuid" => env("HDFC_KEY_UUID"),
    "private_key_path" => storage_path("app/private/keys/privateKey.pem"),
    "public_key_path" => storage_path("app/private/keys/key_05e37906177a440fb02004d44e084514.pem"),
    "base_url" => env("HDFC_BASE_URL", "https://smartgateway.hdfcuat.bank.in"),
];
