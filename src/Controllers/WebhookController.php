<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

class WebhookController extends BaseController
{
    /**
     * Handle incoming webhook events
     */
    public function handleWebhook()
    {
        try {
            // Get the webhook signature from headers
            $signature = $_SERVER['HTTP_X_USEEPAY_SIGNATURE'] ?? '';
            
            // Get the raw POST data
            $payload = file_get_contents('php://input');
            
            if (empty($payload)) {
                throw new \Exception('No payload received');
            }
            
            // Verify the webhook signature (you should implement this)
            $this->verifyWebhookSignature($signature, $payload);
            
            // Decode the JSON payload
            $event = json_decode($payload, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON payload: ' . json_last_error_msg());
            }
            
            // Log the webhook event
            $this->logWebhookEvent($event);
            
            // Process the event based on its type
            $this->processEvent($event);
            
            // Return a 200 OK response to acknowledge receipt
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Webhook received']);
            exit;
            
        } catch (\Exception $e) {
            // Log the error
            error_log('Webhook error: ' . $e->getMessage());
            
            // Return a non-200 status code to indicate failure
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    /**
     * Verify the webhook signature
     * 
     * @param string $signature
     * @param string $payload
     * @throws \Exception
     */
    private function verifyWebhookSignature($signature, $payload)
    {
        global $config;
        
        $webhookSecret = $config['usee_pay']['webhook_secret'] ?? '';
        
        if (empty($webhookSecret)) {
            throw new \Exception('Webhook secret is not configured');
        }
        
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid webhook signature');
        }
    }
    
    /**
     * Log webhook events for debugging
     * 
     * @param array $event
     */
    private function logWebhookEvent($event)
    {
        $logDir = __DIR__ . '/../../logs';
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/webhooks.log';
        $timestamp = date('Y-m-d H:i:s');
        $eventType = $event['type'] ?? 'unknown';
        $eventId = $event['id'] ?? 'unknown';
        
        $logMessage = sprintf(
            "[%s] %s (%s): %s\n",
            $timestamp,
            $eventType,
            $eventId,
            json_encode($event, JSON_PRETTY_PRINT)
        );
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Process different types of webhook events
     * 
     * @param array $event
     */
    private function processEvent($event)
    {
        $eventType = $event['type'] ?? '';
        $objectType = $event['data']['object_type'] ?? '';
        $object = $event['data']['object'] ?? [];
        
        // Log the event type for debugging
        error_log("Processing webhook event: {$eventType} ({$objectType})");
        
        // Handle different event types
        switch ($eventType) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($object);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($object);
                break;
                
            case 'charge.refunded':
                $this->handleChargeRefunded($object);
                break;
                
            case 'customer.subscription.created':
                $this->handleSubscriptionCreated($object);
                break;
                
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($object);
                break;
                
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($object);
                break;
                
            default:
                // Log unhandled event types
                error_log("Unhandled webhook event type: {$eventType}");
                break;
        }
    }
    
    // Event handlers for different webhook events
    
    private function handlePaymentSucceeded($paymentIntent)
    {
        // Update your database to reflect that the payment was successful
        $paymentId = $paymentIntent['id'] ?? 'unknown';
        $amount = $paymentIntent['amount'] / 100; // Convert from cents to dollars
        $currency = strtoupper($paymentIntent['currency'] ?? 'USD');
        
        error_log("Payment succeeded: {$paymentId} - {$amount} {$currency}");
        
        // Here you would typically update your database, send confirmation emails, etc.
    }
    
    private function handlePaymentFailed($paymentIntent)
    {
        $paymentId = $paymentIntent['id'] ?? 'unknown';
        $error = $paymentIntent['last_payment_error']['message'] ?? 'Unknown error';
        
        error_log("Payment failed: {$paymentId} - {$error}");
        
        // Here you would typically notify your team, update your database, etc.
    }
    
    private function handleChargeRefunded($charge)
    {
        $chargeId = $charge['id'] ?? 'unknown';
        $amount = $charge['amount_refunded'] / 100;
        $currency = strtoupper($charge['currency'] ?? 'USD');
        
        error_log("Charge refunded: {$chargeId} - {$amount} {$currency}");
        
        // Here you would typically update your database, process the refund, etc.
    }
    
    private function handleSubscriptionCreated($subscription)
    {
        $subscriptionId = $subscription['id'] ?? 'unknown';
        $customerId = $subscription['customer'] ?? 'unknown';
        
        error_log("Subscription created: {$subscriptionId} for customer {$customerId}");
        
        // Here you would typically update your database, send welcome email, etc.
    }
    
    private function handleSubscriptionUpdated($subscription)
    {
        $subscriptionId = $subscription['id'] ?? 'unknown';
        $status = $subscription['status'] ?? 'unknown';
        
        error_log("Subscription updated: {$subscriptionId} - Status: {$status}");
        
        // Here you would typically update your database, handle plan changes, etc.
    }
    
    private function handleSubscriptionDeleted($subscription)
    {
        $subscriptionId = $subscription['id'] ?? 'unknown';
        
        error_log("Subscription deleted: {$subscriptionId}");
        
        // Here you would typically update your database, send cancellation email, etc.
    }
}

// Handle the webhook request
$controller = new WebhookController();
$controller->handleWebhook();
