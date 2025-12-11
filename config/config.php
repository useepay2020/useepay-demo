<?php

return [
    'app' => [
        'name' => 'UseePay API Documentation',
        'version' => '1.0.0',
        'environment' => getenv('APP_ENV') ?: 'development',
        'debug' => getenv('APP_DEBUG') ?: true,
    ],
    'usee_pay' => [
        'api_public_key' => getenv('USEEPAY_PUBLIC_API_KEY') ?: 'UseePay_PK_TEST_bs16i07Yo9hEKIDKA69gwj5U5v6U8ebZRCMJOOWwraoJHgeawPHlsALP8K3fAOJe1puHa6IiRrTNBMuTlnYXfLWerhXHReKAWrmY',
        'api_private_key' => getenv('USEEPAY_PRIVATE_API_KEY') ?: 'UseePay_SK_TEST_R44tyTYEJ41bdQzFatdEtED9fbqEBVP1uHaRb145glbGdgvHeWIMQDf26wuWfdc1H1UNHw1G6UrPULktzhk0tpHfsMN88xDjjL1x',
        'merchant_no' => getenv('USEEPAY_MERCHANT_NO') ?: '500000000014542',
        'app_id' => getenv('USEEPAY_APP_ID') ?: 'www.paycc.com',
        'environment' => getenv('USEEPAY_ENV') ?: 'sandbox',
        'timeout' => [
            'connect' => 30,  // 连接超时时间（秒）
            'read' => 60      // 读取超时时间（秒）
        ],
        'callback_url' => getenv('USEEPAY_CALLBACK_URL') ?: 'https://demo1.uat.useepay.com/payment/callback',
    ]
];
