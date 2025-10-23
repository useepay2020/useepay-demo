<?php

return [
    'app' => [
        'name' => 'UseePay API Documentation',
        'version' => '1.0.0',
        'environment' => getenv('APP_ENV') ?: 'development',
        'debug' => getenv('APP_DEBUG') ?: true,
    ],
    'usee_pay' => [
        'api_key' => getenv('USEEPAY_API_KEY') ?: 'UseePay_SK_ZPhc8g0q5RQwcV1FLUdyupD2jMqjtGNMFnZOKTRyr7q5lnz4xkGWWpx0TV1mxc4XM33MEba2WKcE1sHKeMDwwBgWgXUP9O1hpVoe',
        'merchant_no' => getenv('USEEPAY_MERCHANT_NO') ?: '500000000011023',
        'app_id' => getenv('USEEPAY_APP_ID') ?: 'www.pay.com',
        'environment' => getenv('USEEPAY_ENV') ?: 'sandbox',
        'sandbox_url' => 'https://openapi1.uat.useepay.com',
        'production_url' => 'https://openapi.useepay.com',
        'timeout' => [
            'connect' => 30,  // 连接超时时间（秒）
            'read' => 60      // 读取超时时间（秒）
        ],
        'callback_url' => getenv('USEEPAY_CALLBACK_URL') ?: 'http://localhost:8000/payment/callback',
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'database' => getenv('DB_DATABASE') ?: 'useepay_demo',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ]
];
