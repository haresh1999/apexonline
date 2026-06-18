<?php

return [
    'sandbox' => [
        "merchant_id" => env("HDFC_MERCHANT_ID"),
        "key_uuid" => env("HDFC_KEY_UUID"),
        "private_key_path" => storage_path("app/private/sandbox_keys/privateKey.pem"),
        "public_key_path" => storage_path("app/private/sandbox_keys/key_05e37906177a440fb02004d44e084514.pem"),
        "base_url" => env("HDFC_BASE_URL", "https://smartgateway.hdfc.bank.in"),
    ],
    'production' => [
        "merchant_id" => env("HDFC_MERCHANT_ID"),
        "key_uuid" => env("HDFC_KEY_UUID"),
        "private_key_path" => storage_path("app/private/sandbox_keys/privateKey.pem"),
        "public_key_path" => storage_path("app/private/sandbox_keys/key_61c5ad5e41da4693a6c68594a8cb5138.pem"),
        "base_url" => env("HDFC_BASE_URL", "https://smartgateway.hdfc.bank.in"),
    ]
];
