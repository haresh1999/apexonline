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
    'signature_key' => [
        'production' => '09e820e3-b7b6-438d-93ed-f5cdb3c39d93',
        'sandbox' => '5222a6fe-faac-4c0b-a4b8-f449a4f0899e'
    ],

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
            'merchant' => 'EDU TRADE',
            'merchant_id' => 'M23PMXE558QOM',
            'client_id' => 'TEST-M23PMXE558QOM_25101',
            'client_version' => 1,
            'client_secret' => 'NDQ5ODdhMDYtN2YzOC00ZTQzLWE5NGYtODQxYTRjM2E1YWJi',
            'grant_type' => 'client_credentials',
        ],
        'production' => [
            'merchant' => 'EDU TRADE',
            'merchant_id' => 'M23PMXE558QOM',
            'client_id' => 'SU2509231940115728161187',
            'client_version' => 1,
            'client_secret' => 'ef6c3f40-db7b-4839-b97d-cc114df6d895',
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
            'mid' => 'RsTmrE01629973240425',
            'mkey' => 'bOuZxyutTrfL1DKV',
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
    ]
];
