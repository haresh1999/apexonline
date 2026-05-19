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
            'client_code' => 'DCRBP',
            'username' => 'userph.jha_3036',
            'password' => 'DBOI1_SP3036',
            'auth_key' => '0jeOYcu3UnfmWyLC',
            'auth_iv' => 'C28LAmGxXTqmK0QJ',
        ],
        'production' => [
            'client_code' => 'SHA9I6',
            'username' => 'ikondubai@gmail.com',
            'password' => 'SHA9I6_SP23560',
            'auth_key' => 'HT3sbrw8jrzKBmZqt0Wr7iFWZaq9mWy5j8d/Yu7WQxE=',
            'auth_iv' => '0Op4vCNftDe4i0OwdOfP99BW2qz8KCfOzyXz66VWL4L2q50/uzB2ygpF6Xq+2Vea',
        ],
    ],

    'razorpay' => [
        'sandbox' => [
            'key_id' => 'rzp_test_RXE0uWen59xOZ0',
            'key_secret' => 'PRq5jSWVM7VvKc5fqEdaqhWn',
        ],
        'production' => [
            'key_id' => 'rzp_live_RTIzeZS7Q22shx',
            'key_secret' => 'nca3Agw8XlpW95Hfmp4et4al',
        ],
    ],

    'phonepe' => [
        'sandbox' => [
            'merchant' => 'APEX ONLINE',
            'merchant_id' => 'M22PWSTHY7KC4',
            'client_id' => 'M22PWSTHY7KC4_2605051809',
            'client_version' => 1,
            'client_secret' => 'NTU5MjIyMjktYjI2Yy00MDRmLWE3YTktMDQ3NGU4NzMwMjc4',
            'grant_type' => 'client_credentials',
        ],
        'production' => [
            'merchant' => 'APEX ONLINE',
            'merchant_id' => 'M22PWSTHY7KC4',
            'client_id' => 'SU2605051733192705903672',
            'client_version' => 1,
            'client_secret' => 'dd4d5212-193b-4461-9abc-b67fd368a0bf',
            'grant_type' => 'client_credentials',
        ],
    ],

    'paytm' => [
        'sandbox' => [
            'mid' => 'Resell00448805757124',
            'mkey' => 'KXHUJH&Ywq9pUkkr',
            'website' => 'WEBSTAGING',
            'industry_type' => 'Retail',
            'cid' => 'WEB'
        ],
        'production' => [
            'mid' => 'Zxxphk24061617821448',
            'mkey' => 'kAUX7qdEW2eA1Rk9',
            'website' => 'DEFAULT',
            'industry_type' => 'Retail',
            'cid' => 'WEB'
        ]
    ],

    'ccavenue' => [
        'sandbox' => [
            'merchant_id' => '2507157',
            'working_key' => '1F81381797D5E96BF4AD22BB0CE221C0',
            'access_code' => 'AVYW05KF00CC49WYCC',
            'post_url' => 'https://test.ccavenue.com/transaction/transaction.do',
        ],
        'production' => [
            'merchant_id' => '',
            'working_key' => '',
            'access_code' => '',
            'post_url' => 'https://secure.ccavenue.com/transaction/transaction.do',
        ]
    ],

    'zoho' => [
        'sandbox' => [
            'api_key' => '1003.cd4be6050fd2f1923bfe1fec83e4ca92.8c029a083c31cd9b95cda03041145198',
            'signing_key' => 'd5439670e4af85a519da1187a370a73f790e4691382c2e9a9948ced64d184a077704442ad5450e5e5494273e4a67c8df', //connector key
        ],
        'production' => [
            'api_key' => '1003.cd4be6050fd2f1923bfe1fec83e4ca92.8c029a083c31cd9b95cda03041145198',
            'signing_key' => 'd5439670e4af85a519da1187a370a73f790e4691382c2e9a9948ced64d184a077704442ad5450e5e5494273e4a67c8df',
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
