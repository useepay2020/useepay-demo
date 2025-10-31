<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

class PaymentController extends BaseController
{

    /**
     * Handle payment creation
     */
    public function createPayment()
    {
        global $config;
        // Validate required fields
        $requiredFields = array('items', 'totals');
        $data = $this->getRequestData();
        $missing = [];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            $this->errorResponse('Missing required fields: ' . implode(', ', $missing), 400);
            return;
        }

        // Validate items
        if (!is_array($data['items']) || count($data['items']) === 0) {
            $this->errorResponse('Cart is empty', 400);
            return;
        }

        try {

            // Generate unique order ID
            $orderId = 'ORD_' . time() . '_' . rand(1000, 9999);

            // Convert total amount to cents (USD)
            $totalAmount = floatval($data['totals']['totalAmount']);
            $currency = $data['totals']['currency'];
            // Prepare customer information
            $customerName = $data['firstName'] . ' ' . $data['lastName'];

            // Prepare items description
            $itemsDescription = array();
            foreach ($data['items'] as $item) {
                $itemsDescription[] = $item['name'] . ' x' . $item['quantity'];
            }
            $description = 'Order: ' . implode(', ', $itemsDescription);

            // Prepare order items
            $orderItems = array_map(function($item) use ($currency) {
                return [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'currency' =>$currency,
                    'description' => $item['description']
                ];
            }, $data['items']);

            // Get client IP address
            $ipAddress = '0.0.0.0';
            $ipSources = array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            );

            foreach ($ipSources as $source) {
                if (!empty($_SERVER[$source])) {
                    $ipAddress = $_SERVER[$source];
                    break;
                }
            }

            // Get browser information
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
            
            // Try to get browser info, with fallback if browscap is not configured
            $browserInfo = array();
            if (function_exists('get_browser')) {
                try {
                    $browserInfo = @get_browser(null, true);
                    if (!$browserInfo) {
                        $browserInfo = array();
                    }
                } catch (\Exception $e) {
                    // Silently fail if browscap is not available
                    $browserInfo = array();
                }
            }

            // Prepare device data
            $deviceData = array(
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'browser' => array(
                    'browser' => isset($browserInfo['browser']) ? $browserInfo['browser'] : 'Unknown',
                    'version' => isset($browserInfo['version']) ? $browserInfo['version'] : 'Unknown',
                    'platform' => isset($browserInfo['platform']) ? $browserInfo['platform'] : 'Unknown',
                    'device_type' => isset($browserInfo['device_type']) ? $browserInfo['device_type'] : 'Unknown',
                    'is_mobile' => isset($browserInfo['ismobiledevice']) ? (bool)$browserInfo['ismobiledevice'] : false,
                    'is_tablet' => isset($browserInfo['istablet']) ? (bool)$browserInfo['istablet'] : false,
                    'is_crawler' => isset($browserInfo['crawler']) ? (bool)$browserInfo['crawler'] : false
                ),
                'accept_language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
                'request_time' => date('Y-m-d H:i:s'),
                'request_method' => $_SERVER['REQUEST_METHOD']
            );

            // Create payment intent parameters
            $paymentParams = array(
                'amount' => $totalAmount,
                'currency' => $currency,
                'description' => $description,
                'merchant_order_id' => $orderId,
                'device_data' => $deviceData,
                'return_url' => $config['usee_pay']['callback_url'],
                'order' => array(
                    'products' => $orderItems,
                    'shipping' => array(
                        'address' => array(
                            'line1' => $data['address'],
                            'city' => $data['city'],
                            'state' => $data['state'],
                            'postcode' => $data['zipCode'],
                            'country' => $data['country']
                        ),
                        'first_name' => $data['firstName'],
                        'last_name' => $data['lastName'],
                        'name' => $customerName,
                        'email' => $data['email'],
                        'phone' => $data['phone']
                    )
                ),
            );

            // Only add payment_method_types if paymentMethod is provided
            if (!empty($data['paymentMethods'])) {
                $paymentParams['payment_method_types'] = $data['paymentMethods'];
            }

            // Add customer info only if email or phone is not empty
            if (!empty($data['email']) || !empty($data['phone'])) {
                $paymentParams['customer'] = array(
                    'merchant_customer_id' => 'CUST_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8),
                    'first_name' => $data['firstName'],
                    'last_name' => $data['lastName'],
                    'name' => $customerName,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => array(
                        'line1' => $data['address'],
                        'city' => $data['city'],
                        'state' => $data['state'],
                        'postcode' => $data['zipCode'],
                        'country' => $data['country']
                    )
                );
            }

            // Log API request parameters
            $this->log('UseePay API Request - createPayment', 'info', $paymentParams, 'payment');

            // Create payment intent
            $client = $this->getUseePayClient();
            $paymentIntent = $client->paymentIntents()->create($paymentParams);
            
            // Log API response
            $this->log('UseePay API Response - createPayment', 'info', $paymentIntent, 'payment');
            
            $this->jsonResponse($paymentIntent);
            
        } catch (\Exception $e) {
            $this->errorResponse('Payment creation failed: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Retrieve payment details
     */
    public function getPayment($paymentId)
    {
        if (empty($paymentId)) {
            $this->errorResponse('Payment ID is required', 400);
            return;
        }

        try {
            // Log API request
            $this->log('UseePay API Request - getPayment', 'info', [
                'method' => 'paymentIntents.retrieve',
                'payment_id' => $paymentId
            ], 'api');
            
            $client = $this->getUseePayClient();
            $payment = $client->paymentIntents()->retrieve($paymentId);
            
            // Log API response
            $this->log('UseePay API Response - getPayment', 'info', [
                'method' => 'paymentIntents.retrieve',
                'status' => 'success',
                'payment_id' => $paymentId,
                'payment_status' => isset($payment['status']) ? $payment['status'] : 'N/A'
            ], 'api');
            
            $this->jsonResponse([
                'payment' => $payment
            ]);
            
        } catch (\Exception $e) {
            // Log API error
            $this->log('UseePay API Response - getPayment', 'error', [
                'method' => 'paymentIntents.retrieve',
                'status' => 'failed',
                'payment_id' => $paymentId,
                'error_message' => $e->getMessage()
            ], 'api');
            
            $this->errorResponse('Failed to retrieve payment: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle payment confirmation
     */
    public function confirmPayment($paymentId)
    {
        if (empty($paymentId)) {
            $this->errorResponse('Payment ID is required', 400);
            return;
        }

        try {
            $data = $this->getRequestData();
            
            // Log API request
            $this->log('UseePay API Request - confirmPayment', 'info', [
                'method' => 'paymentIntents.confirm',
                'payment_id' => $paymentId,
                'payment_method' => isset($data['payment_method']) ? $data['payment_method'] : 'N/A',
                'has_return_url' => isset($data['return_url']) ? 'yes' : 'no'
            ], 'api');
            
            $client = $this->getUseePayClient();
            
            $payment = $client->paymentIntents()->confirm(
                $paymentId,
                [
                    'payment_method' => $data['payment_method'] ?? null,
                    'return_url' => $data['return_url'] ?? null
                ]
            );
            
            // Log API response
            $this->log('UseePay API Response - confirmPayment', 'info', [
                'method' => 'paymentIntents.confirm',
                'status' => 'success',
                'payment_id' => $paymentId,
                'payment_status' => isset($payment['status']) ? $payment['status'] : 'N/A'
            ], 'api');
            
            $this->jsonResponse([
                'status' => $payment['status'],
                'payment' => $payment
            ]);
            
        } catch (\Exception $e) {
            // Log API error
            $this->log('UseePay API Response - confirmPayment', 'error', [
                'method' => 'paymentIntents.confirm',
                'status' => 'failed',
                'payment_id' => $paymentId,
                'error_message' => $e->getMessage()
            ], 'api');
            
            $this->errorResponse('Payment confirmation failed: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle payment cancellation
     */
    public function cancelPayment($paymentId)
    {
        if (empty($paymentId)) {
            $this->errorResponse('Payment ID is required', 400);
            return;
        }

        try {
            // Log API request
            $this->log('UseePay API Request - cancelPayment', 'info', [
                'method' => 'paymentIntents.cancel',
                'payment_id' => $paymentId
            ], 'api');
            
            $client = $this->getUseePayClient();
            $cancellation = $client->paymentIntents()->cancel($paymentId);
            
            // Log API response
            $this->log('UseePay API Response - cancelPayment', 'info', [
                'method' => 'paymentIntents.cancel',
                'status' => 'success',
                'payment_id' => $paymentId,
                'cancelled' => true
            ], 'api');
            
            $this->jsonResponse([
                'cancelled' => true,
                'payment_id' => $paymentId,
                'details' => $cancellation
            ]);
            
        } catch (\Exception $e) {
            // Log API error
            $this->log('UseePay API Response - cancelPayment', 'error', [
                'method' => 'paymentIntents.cancel',
                'status' => 'failed',
                'payment_id' => $paymentId,
                'error_message' => $e->getMessage()
            ], 'api');
            
            $this->errorResponse('Failed to cancel payment: ' . $e->getMessage(), 500);
        }
    }


}
