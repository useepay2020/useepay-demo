<?php

/**
 * UseePay PHP SDK - Subscription Example (PHP 5.3+)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

// Initialize authentication
$authentication = new Authentication(
    '5000000***',
    'www.***.com',
    'UseePay_SK_***'
);

// Create client
$client = UseePayClient::withEnvironment(
    ApiEnvironment::SANDBOX,
    $authentication
);

echo "=== UseePay PHP SDK - Subscription Examples (PHP 5.3+) ===\n\n";

try {
    // Example 1: Create a Subscription
    echo "1. Creating a subscription...\n";
    
    $params = array(
        'customer_id' => 'cus_123456',
        'recurring' => array(
            'interval' => 'month',
            'interval_count' => 1,
            'unit_amount' => 2999
        ),
        'currency' => 'USD',
        'description' => 'Monthly Premium Plan',
        'metadata' => array(
            'plan' => 'premium',
            'billing_cycle' => 'monthly'
        )
    );
    
    $subscription = $client->subscriptions()->create($params);
    
    echo "Subscription created successfully!\n";
    echo "  ID: " . (isset($subscription['id']) ? $subscription['id'] : 'N/A') . "\n";
    echo "  Status: " . (isset($subscription['status']) ? $subscription['status'] : 'N/A') . "\n";
    echo "  Currency: " . (isset($subscription['currency']) ? $subscription['currency'] : 'N/A') . "\n\n";
    
    $subscriptionId = isset($subscription['id']) ? $subscription['id'] : null;
    
    if ($subscriptionId) {
        // Example 2: Retrieve Subscription
        echo "2. Retrieving subscription...\n";
        $retrieved = $client->subscriptions()->retrieve($subscriptionId);
        echo "Subscription retrieved: " . (isset($retrieved['id']) ? $retrieved['id'] : 'N/A') . "\n\n";
        
        // Example 3: Update Subscription
        echo "3. Updating subscription...\n";
        $updateParams = array(
            'description' => 'Updated Monthly Premium Plan',
            'metadata' => array(
                'updated_at' => date('Y-m-d H:i:s')
            )
        );
        
        $updated = $client->subscriptions()->update($subscriptionId, $updateParams);
        echo "Subscription updated: " . (isset($updated['description']) ? $updated['description'] : 'N/A') . "\n\n";
        
        // Example 4: List Subscriptions
        echo "4. Listing subscriptions...\n";
        $list = $client->subscriptions()->listSubscriptions();
        echo "Retrieved subscriptions list\n\n";
    }
    
    echo "=== All subscription examples completed! ===\n";
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}
