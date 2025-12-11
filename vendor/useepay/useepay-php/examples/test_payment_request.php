<?php
/**
 * Test Payment Request
 */

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

echo "=== Testing UseePay Payment Request ===\n\n";

try {
    // Initialize authentication
    $authentication = new Authentication(
        '500000000011101',
        'www.demo.com',
        'UseePay_SK_OJXDEGtza8fqJlLp61JczT2sFlpDBJiq4Co6zwOZs9wULfyDBz5wk2G9xRdRTp4wB1kTjwWV4bJs5hg6CMpgfxQ11ZggtGXcaoS9'
    );

    echo "✓ Authentication initialized\n";

    // Create client with sandbox environment
    $client = UseePayClient::withEnvironment(
        ApiEnvironment::SANDBOX,
        $authentication
    );

    echo "✓ Client created\n";

    // Set explicit timeouts (in seconds)
    UseePay::setConnectTimeout(30);
    UseePay::setReadTimeout(60);
    
    echo "✓ Timeouts set: Connect=30s, Read=60s\n";

    // Disable SSL verification for development
    UseePay::setVerifySslCerts(false);
    
    echo "✓ SSL verification disabled\n\n";

    // Prepare minimal payment parameters
    $paymentParams = array(
        'amount' => 100.00,
        'currency' => 'USD',
        'description' => 'Test Payment',
        'merchant_order_id' => 'TEST_' . time(),
        'customer' => array(
            'merchant_customer_id' => 'CUST_TEST_' . time(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'address' => array(
                'line1' => '123 Test St',
                'city' => 'Test City',
                'state' => 'TC',
                'postal_code' => '12345',
                'country' => 'US'
            )
        )
    );

    echo "Sending payment request...\n";
    echo "Parameters: " . json_encode($paymentParams, JSON_PRETTY_PRINT) . "\n\n";

    $start = microtime(true);
    $paymentIntent = $client->paymentIntents()->create($paymentParams);
    $duration = microtime(true) - $start;

    echo "✓ Payment request successful!\n";
    echo "Duration: " . round($duration, 3) . " seconds\n";
    echo "Response: " . json_encode($paymentIntent, JSON_PRETTY_PRINT) . "\n";

} catch (Exception $e) {
    echo "\n✗ Error occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
