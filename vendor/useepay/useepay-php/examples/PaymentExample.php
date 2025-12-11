<?php

/**
 * UseePay PHP SDK - Payment Example (PHP 5.3+)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

// Initialize authentication
$authentication = new Authentication(
    '500000000011101',
    'www.demo.com',
    'UseePay_SK_OJXDEGtza8fqJlLp61JczT2sFlpDBJiq4Co6zwOZs9wULfyDBz5wk2G9xRdRTp4wB1kTjwWV4bJs5hg6CMpgfxQ11ZggtGXcaoS9'
);

// Create client with sandbox environment
$client = UseePayClient::withEnvironment(
    ApiEnvironment::SANDBOX,
    $authentication
);
// 临时禁用 SSL 验证（仅用于开发测试）
UseePay::setVerifySslCerts(false);

echo "=== UseePay PHP SDK - Payment Examples (PHP 5.3+) ===\n\n";

try {
    // Example 1: Create a Payment Intent
    echo "1. Creating a payment intent...\n";
    
    $params = array(
        'amount' => 5000,
        'currency' => 'USD',
        'description' => 'Order #12345',
        'merchant_order_id' => 'ORDER_' . time(),
        'metadata' => array(
            'order_id' => '12345',
            'customer_name' => 'John Doe'
        )
    );
    
    $paymentIntent = $client->paymentIntents()->create($params);
    
    echo "Payment intent created successfully!\n";
    echo "  ID: " . (isset($paymentIntent['id']) ? $paymentIntent['id'] : 'N/A') . "\n";
    echo "  Amount: $" . number_format((isset($paymentIntent['amount']) ? $paymentIntent['amount'] : 0) / 100, 2) . "\n";
    echo "  Status: " . (isset($paymentIntent['status']) ? $paymentIntent['status'] : 'N/A') . "\n\n";
    
    $paymentIntentId = isset($paymentIntent['id']) ? $paymentIntent['id'] : null;
    
    if ($paymentIntentId) {
        // Example 2: Retrieve Payment Intent
        echo "2. Retrieving payment intent...\n";
        $retrieved = $client->paymentIntents()->retrieve($paymentIntentId);
        echo "Payment intent retrieved: " . (isset($retrieved['id']) ? $retrieved['id'] : 'N/A') . "\n\n";
        
        // Example 3: Create a Refund
        echo "3. Creating a refund...\n";
        $refundParams = array(
            'payment_intent_id' => $paymentIntentId,
            'amount' => 2000,
            'reason' => 'customer_request'
        );
        
        $refund = $client->refunds()->create($refundParams);
        echo "Refund created: " . (isset($refund['id']) ? $refund['id'] : 'N/A') . "\n\n";
    }
    
    echo "=== All examples completed successfully! ===\n";
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}
