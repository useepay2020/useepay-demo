<?php

return [
    'app' => [
        'name' => 'UseePay API Documentation',
        'version' => '1.0.0',
        'environment' => getenv('APP_ENV') ?: 'development',
        'debug' => getenv('APP_DEBUG') ?: true,
    ],
    'usee_pay' => [
        'api_key' => getenv('USEEPAY_API_KEY') ?: 'UseePay_SK_............',
        'merchant_no' => getenv('USEEPAY_MERCHANT_NO') ?: '5000000000.....',
        'app_id' => getenv('USEEPAY_APP_ID') ?: 'www.xxxxx.com',
        'environment' => getenv('USEEPAY_ENV') ?: 'sandbox',
        'timeout' => [
            'connect' => 30,  // 连接超时时间（秒）
            'read' => 60      // 读取超时时间（秒）
        ],
        'callback_url' => getenv('USEEPAY_CALLBACK_URL') ?: 'http://localhost:8000/payment/callback',
    ]
];
