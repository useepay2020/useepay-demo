<?php

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;
use UseePay\Param\Customer\CustomerCreateParams;

// Initialize authentication
$authentication = new Authentication(
    '500000000011101',
    'www.demo.com',
    'UseePay_SK_OJXDEGtza8fqJlLp61JczT2sFlpDBJiq4Co6zwOZs9wULfyDBz5wk2G9xRdRTp4wB1kTjwWV4bJs5hg6CMpgfxQ11ZggtGXcaoS9'
);
// 临时禁用 SSL 验证（仅用于开发测试）
UseePay::setVerifySslCerts(false);

// Create client
$client = UseePayClient::withEnvironment(
    ApiEnvironment::SANDBOX,
    $authentication
);
// Set explicit timeouts (in seconds)
UseePay::setConnectTimeout(30);  // 30 seconds for connection
UseePay::setReadTimeout(60);     // 60 seconds for reading response

echo "=== UseePay PHP SDK Example (PHP 5.3+) ===\n\n";

try {
    // Create a customer
    $params = new CustomerCreateParams();
    $params->name = 'John Doe';
    $params->email = 'john@example.com';
    $params->phone = '1234567890';
    $params->merchantCustomerId = 'CUST_' . time();
    
    $customer = $client->customers()->create($params);
    
    echo "Customer created: " . $customer->id . "\n";
    echo "Name: " . $customer->name . "\n";
    echo "Email: " . $customer->email . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
