<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'env' => env('APP_MODE', 'production'),
    'user' => [],

    'sabpaisa' => [
        'sandbox' => [
            'client_code' => env('SABPAISA_SANDBOX_CLIENT_CODE'),
            'api_key' => env('SABPAISA_SANDBOX_API_KEY'),
            'secret' => env('SABPAISA_SANDBOX_SECRET'),
        ],
        'production' => [
            'client_code' => env('SABPAISA_CLIENT_CODE'),
            'api_key' => env('SABPAISA_API_KEY'),
            'secret' => env('SABPAISA_SECRET'),
        ],
    ],

    'razorpay' => [
        'sandbox' => [
            'key_id' => env('RAZORPAY_SANDBOX_KEY_ID'),
            'key_secret' => env('RAZORPAY_SANDBOX_KEY_SECRET'),
        ],
        'production' => [
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
        ],
    ],

    'phonepe' => [
        'sandbox' => [
            'merchant' => env('PHONEPE_SANDBOX_MERCHANT'),
            'merchant_id' => env('PHONEPE_SANDBOX_MERCHANT_ID'),
            'client_id' => env('PHONEPE_SANDBOX_CLIENT_ID'),
            'client_version' => env('PHONEPE_SANDBOX_CLIENT_VERSION'),
            'client_secret' => env('PHONEPE_SANDBOX_CLIENT_SECRET'),
            'grant_type' => env('PHONEPE_SANDBOX_GRANT_TYPE'),
        ],
        'production' => [
            'merchant' => env('PHONEPE_MERCHANT'),
            'merchant_id' => env('PHONEPE_MERCHANT_ID'),
            'client_id' => env('PHONEPE_CLIENT_ID'),
            'client_version' => env('PHONEPE_CLIENT_VERSION'),
            'client_secret' => env('PHONEPE_CLIENT_SECRET'),
            'grant_type' => env('PHONEPE_GRANT_TYPE'),
        ],
    ],

    'paytm' => [
        'sandbox' => [
            'mid' => env('PAYTM_SANDBOX_MID'),
            'mkey' => env('PAYTM_SANDBOX_MKEY'),
            'website' => env('PAYTM_SANDBOX_WEBSITE'),
            'industry_type' => env('PAYTM_SANDBOX_INDUSTRY_TYPE'),
            'cid' => env('PAYTM_SANDBOX_CID')
        ],
        'production' => [
            'mid' => env('PAYTM_MID'),
            'mkey' => env('PAYTM_MKEY'),
            'website' => env('PAYTM_WEBSITE'),
            'industry_type' => env('PAYTM_INDUSTRY_TYPE'),
            'cid' => env('PAYTM_CID')
        ]
    ],

    'ccavenue' => [
        'sandbox' => [
            'merchant_id' => env('CCAVENUE_SANDBOX_MERCHANT_ID'),
            'working_key' => env('CCAVENUE_SANDBOX_WORKING_KEY'),
            'access_code' => env('CCAVENUE_SANDBOX_ACCESS_CODE'),
            'post_url' => env('CCAVENUE_SANDBOX_POST_URL'),
        ],
        'production' => [
            'merchant_id' => env('CCAVENUE_MERCHANT_ID'),
            'working_key' => env('CCAVENUE_WORKING_KEY'),
            'access_code' => env('CCAVENUE_ACCESS_CODE'),
            'post_url' => env('CCAVENUE_POST_URL'),
        ]
    ],

    'zoho' => [
        'sandbox' => [
            'api_key' => env('ZOHO_SANDBOX_API_KEY'),
            'signing_key' => env('ZOHO_SANDBOX_SIGNING_KEY'),
        ],
        'production' => [
            'api_key' => env('ZOHO_API_KEY'),
            'signing_key' => env('ZOHO_SIGNING_KEY'),
        ]
    ],

    'payu' => [
        'sandbox' => [
            'payu_key' => env('PAYU_SANDBOX_PAYU_KEY'),
            'payu_salt' => env('PAYU_SANDBOX_PAYU_SALT'),
            'payu_url' => env('PAYU_SANDBOX_PAYU_URL'),
        ],
        'production' => [
            'payu_key' => env('PAYU_PAYU_KEY'),
            'payu_salt' => env('PAYU_PAYU_SALT'),
            'payu_url' => env('PAYU_PAYU_URL'),
        ]
    ],

    'cashfree' => [
        'sandbox' => [
            'app_id' => env('CASHFREE_SANDBOX_APP_ID'),
            'secret_key' => env('CASHFREE_SANDBOX_SECRET_KEY'),
        ],
        'production' => [
            'app_id' => env('CASHFREE_APP_ID'),
            'secret_key' => env('CASHFREE_SECRET_KEY'),
        ],
    ],

    'instamojo' => [
        'sandbox' => [
            'client_id' => env('INSTAMOJO_SANDBOX_CLIENT_ID'),
            'client_secret' => env('INSTAMOJO_SANDBOX_CLIENT_SECRET'),
            'api_key' => env('INSTAMOJO_SANDBOX_API_KEY'),
            'auto_token' => env('INSTAMOJO_SANDBOX_AUTO_TOKEN'),
            'salt_key' => env('INSTAMOJO_SANDBOX_SALT_KEY'),
        ],
        'production' => [
            'client_id' => env('INSTAMOJO_CLIENT_ID'),
            'client_secret' => env('INSTAMOJO_CLIENT_SECRET'),
            'api_key' => env('INSTAMOJO_API_KEY'),
            'auto_token' => env('INSTAMOJO_AUTO_TOKEN'),
            'salt_key' => env('INSTAMOJO_SALT_KEY'),
        ]
    ]
];
