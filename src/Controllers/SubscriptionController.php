<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

class SubscriptionController extends BaseController
{
    /**
     * Create a new subscription
     */
    public function createSubscriptionToPay()
    {
        global $config;
        try {
            $this->log('createSubscription() called', 'info', [], 'subscription');
            
            $data = $this->getRequestData();
            $this->log('Request data received', 'info', $data, 'subscription');
            
            // Validate required fields
            $required = ['customer_id', 'currency', 'recurring'];
            $missing = [];
            
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $missing[] = $field;
                }
            }
            
            if (!empty($missing)) {
                $this->log('Validation failed - missing fields', 'warning', ['missing_fields' => $missing], 'subscription');
                $this->errorResponse(
                    'Missing required fields: ' . implode(', ', $missing),
                    400
                );
            }
            
            $this->log('Validation passed, initializing UseePay client', 'info', [], 'subscription');
            $client = $this->getUseePayClient();
            
            // Create subscription with structured data
            // Handle products array - convert to single product object if needed
            $order = $data['order'] ?? [];

            // Calculate invoice amount from recurring billing
            $unitAmount = $data['recurring']['unit_amount'] ?? 0;

            $subscriptionParams = [
                'customer_id' => $data['customer_id'],
                'currency' => $data['currency'],
                'description' => $data['description'] ?? '',
                'recurring' => [
                    'interval' => $data['recurring']['interval'] ?? 'month',
                    'interval_count' => $data['recurring']['interval_count'] ?? 1,
                    'unit_amount' => $unitAmount,
                    'total_billing_cycles' => $data['recurring']['totalBillingCycles'] ?? null
                ],
                'order' => $order,
            ];

            $this->log('1.Creating subscription', 'info', $subscriptionParams, 'subscription');

            $subscription = $client->subscriptions()->create($subscriptionParams);
            
            $this->log('Subscription created successfully', 'info', ['subscription_id' => $subscription['id'] ?? null], 'subscription');


            // Prepare invoice creation parameters
            $invoiceParams = [
                'subscription_id' => $subscription['id'],
                'customer_id' => $data['customer_id'],
                'currency' => $data['currency'],
                'total_amount' => $unitAmount,
                'description' => $data['description'] ?? 'First invoice for subscription'
            ];
            $this->log('2.Creating first invoice for subscription', 'info', $invoiceParams, 'subscription');

            // Create first invoice after subscription is created
            $invoice = $client->invoices()->create($invoiceParams);

            $this->log('Invoice created successfully', 'info', [
                'invoice_id' => $invoice['id'] ?? null,
                'subscription_id' => $subscription['id']
            ], 'subscription');



            $paymentParams = array(
                'amount' => $unitAmount,
                'currency' => $data['currency'],
                'description' => $data['description'] ?? '',
                'merchant_order_id' => $invoice['id'],
                'return_url' => $config['usee_pay']['callback_url'],
                'customer_id' => $data['customer_id'],
                'device_data' => $this->getDeviceData(),
            );

            if (!empty($data['paymentMethods'])) {
                $paymentParams['payment_method_types'] = $data['paymentMethods'];
            }

            $this->log('3.Creating payment for invoice', 'info', $paymentParams, 'subscription');
            $paymentIntent = $client->paymentIntents()->create($paymentParams);

            $this->log('Payment created successfully', 'info',$paymentIntent , 'subscription');
            $this->jsonResponse($paymentIntent);
            
        } catch (\Exception $e) {
            $this->log('Error creating subscription', 'error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ], 'subscription');
            
            $this->errorResponse(
                'Failed to create subscription: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Retrieve a subscription
     */
    public function getSubscription($subscriptionId)
    {
        try {
            $this->log('getSubscription() called', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $client = $this->getUseePayClient();
            $subscription = $client->subscriptions()->retrieve($subscriptionId);
            
            $this->log('Subscription retrieved successfully', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $this->jsonResponse([
                'subscription' => $subscription
            ]);
            
        } catch (\Exception $e) {
            $this->log('Error retrieving subscription', 'error', [
                'subscription_id' => $subscriptionId,
                'message' => $e->getMessage()
            ], 'subscription');
            
            $this->errorResponse(
                'Failed to retrieve subscription: ' . $e->getMessage(),
                404
            );
        }
    }
    
    /**
     * Update a subscription
     */
    public function updateSubscription($subscriptionId)
    {
        try {
            $this->log('updateSubscription() called', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $data = $this->getRequestData();
            $this->log('Update request data received', 'info', $data, 'subscription');
            
            if (empty($data)) {
                $this->log('No data provided for update', 'warning', ['subscription_id' => $subscriptionId], 'subscription');
                $this->errorResponse('No data provided for update', 400);
            }
            
            $client = $this->getUseePayClient();
            
            // Only include fields that are allowed to be updated
            $updateData = [];
            $allowedFields = [
                'plan_id', 'payment_method_id', 'trial_end', 'metadata'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateData[$field] = $data[$field];
                }
            }
            
            if (empty($updateData)) {
                $this->log('No valid fields provided for update', 'warning', ['subscription_id' => $subscriptionId], 'subscription');
                $this->errorResponse('No valid fields provided for update', 400);
            }
            
            $this->log('Updating subscription', 'info', ['subscription_id' => $subscriptionId, 'update_data' => $updateData], 'subscription');
            $subscription = $client->subscriptions()->update($subscriptionId, $updateData);
            
            $this->log('Subscription updated successfully', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $this->jsonResponse([
                'subscription' => $subscription
            ]);
            
        } catch (\Exception $e) {
            $this->log('Error updating subscription', 'error', [
                'subscription_id' => $subscriptionId,
                'message' => $e->getMessage()
            ], 'subscription');
            
            $this->errorResponse(
                'Failed to update subscription: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Cancel a subscription
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            $this->log('cancelSubscription() called', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $data = $this->getRequestData();
            $client = $this->getUseePayClient();
            
            $cancelAtPeriodEnd = $data['at_period_end'] ?? false;
            $this->log('Cancelling subscription', 'info', [
                'subscription_id' => $subscriptionId,
                'at_period_end' => $cancelAtPeriodEnd
            ], 'subscription');
            
            $cancelled = $client->subscriptions()->cancel(
                $subscriptionId,
                ['at_period_end' => $cancelAtPeriodEnd]
            );
            
            $this->log('Subscription cancelled successfully', 'info', ['subscription_id' => $subscriptionId], 'subscription');
            
            $this->jsonResponse([
                'cancelled' => (bool)$cancelled,
                'subscription_id' => $subscriptionId,
                'at_period_end' => $cancelAtPeriodEnd
            ]);
            
        } catch (\Exception $e) {
            $this->log('Error cancelling subscription', 'error', [
                'subscription_id' => $subscriptionId,
                'message' => $e->getMessage()
            ], 'subscription');
            
            $this->errorResponse(
                'Failed to cancel subscription: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * List subscriptions with pagination
     */
    public function listSubscriptions()
    {
        try {
            $this->log('listSubscriptions() called', 'info', [], 'subscription');
            
            $client = $this->getUseePayClient();
            $params = [
                'limit' => $_GET['limit'] ?? 10,
                'starting_after' => $_GET['starting_after'] ?? null,
                'ending_before' => $_GET['ending_before'] ?? null,
                'customer_id' => $_GET['customer_id'] ?? null,
                'status' => $_GET['status'] ?? null
            ];
            
            $this->log('Listing subscriptions with params', 'info', $params, 'subscription');
            
            $subscriptions = $client->subscriptions()->all($params);
            
            $this->log('Subscriptions retrieved successfully', 'info', [
                'total_count' => $subscriptions['total_count'] ?? 0,
                'has_more' => $subscriptions['has_more'] ?? false
            ], 'subscription');
            
            $this->jsonResponse([
                'data' => $subscriptions['data'] ?? [],
                'has_more' => $subscriptions['has_more'] ?? false,
                'total' => $subscriptions['total_count'] ?? 0
            ]);
            
        } catch (\Exception $e) {
            $this->log('Error listing subscriptions', 'error', [
                'message' => $e->getMessage()
            ], 'subscription');
            
            $this->errorResponse(
                'Failed to list subscriptions: ' . $e->getMessage(),
                500
            );
        }
    }

}

// Handle the request based on HTTP method
$controller = new SubscriptionController();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$endpoint = end($pathParts);

// Route the request
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $controller->createSubscriptionToPay();
        break;
    case 'GET':
        if (is_numeric($endpoint)) {
            $controller->getSubscription($endpoint);
        } else if ($endpoint === 'subscriptions' || $endpoint === '') {
            $controller->listSubscriptions();
        } else {
            $controller->errorResponse('Invalid subscription ID', 400);
        }
        break;
    case 'PUT':
    case 'PATCH':
        if (is_numeric($endpoint)) {
            $controller->updateSubscription($endpoint);
        } else {
            $controller->errorResponse('Invalid subscription ID', 400);
        }
        break;
    case 'DELETE':
        if (is_numeric($endpoint)) {
            $controller->cancelSubscription($endpoint);
        } else {
            $controller->errorResponse('Invalid subscription ID', 400);
        }
        break;
    default:
        $controller->errorResponse('Method not allowed', 405);
        break;
}
