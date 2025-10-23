<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

class SubscriptionController extends BaseController
{
    /**
     * Create a new subscription
     */
    public function createSubscription()
    {
        try {
            $data = $this->getRequestData();
            
            // Validate required fields
            $required = ['customer_id', 'plan_id', 'payment_method_id'];
            $missing = [];
            
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $missing[] = $field;
                }
            }
            
            if (!empty($missing)) {
                $this->errorResponse(
                    'Missing required fields: ' . implode(', ', $missing),
                    400
                );
            }
            
            $client = $this->getUseePayClient();
            
            // Create subscription
            $subscription = $client->subscriptions()->create([
                'customer_id' => $data['customer_id'],
                'plan_id' => $data['plan_id'],
                'payment_method_id' => $data['payment_method_id'],
                'trial_period_days' => $data['trial_period_days'] ?? null,
                'metadata' => $data['metadata'] ?? []
            ]);
            
            $this->jsonResponse([
                'subscription' => $subscription
            ]);
            
        } catch (\Exception $e) {
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
            $client = $this->getUseePayClient();
            $subscription = $client->subscriptions()->retrieve($subscriptionId);
            
            $this->jsonResponse([
                'subscription' => $subscription
            ]);
            
        } catch (\Exception $e) {
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
            $data = $this->getRequestData();
            
            if (empty($data)) {
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
                $this->errorResponse('No valid fields provided for update', 400);
            }
            
            $subscription = $client->subscriptions()->update($subscriptionId, $updateData);
            
            $this->jsonResponse([
                'subscription' => $subscription
            ]);
            
        } catch (\Exception $e) {
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
            $data = $this->getRequestData();
            $client = $this->getUseePayClient();
            
            $cancelAtPeriodEnd = $data['at_period_end'] ?? false;
            
            $cancelled = $client->subscriptions()->cancel(
                $subscriptionId,
                ['at_period_end' => $cancelAtPeriodEnd]
            );
            
            $this->jsonResponse([
                'cancelled' => (bool)$cancelled,
                'subscription_id' => $subscriptionId,
                'at_period_end' => $cancelAtPeriodEnd
            ]);
            
        } catch (\Exception $e) {
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
            $client = $this->getUseePayClient();
            $params = [
                'limit' => $_GET['limit'] ?? 10,
                'starting_after' => $_GET['starting_after'] ?? null,
                'ending_before' => $_GET['ending_before'] ?? null,
                'customer_id' => $_GET['customer_id'] ?? null,
                'status' => $_GET['status'] ?? null
            ];
            
            $subscriptions = $client->subscriptions()->all($params);
            
            $this->jsonResponse([
                'data' => $subscriptions['data'] ?? [],
                'has_more' => $subscriptions['has_more'] ?? false,
                'total' => $subscriptions['total_count'] ?? 0
            ]);
            
        } catch (\Exception $e) {
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
        $controller->createSubscription();
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
