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
                            'line1' => $data['shippingAddress']['address'],
                            'city' => $data['shippingAddress']['city'],
                            'state' => $data['shippingAddress']['state'],
                            'postcode' => $data['shippingAddress']['zipCode'],
                            'country' => $data['shippingAddress']['country']
                        ),
                        'first_name' => $data['firstName'],
                        'last_name' => $data['lastName'],
                        'name' => $customerName,
                        'email' => $data['email'],
                        'phone' => $data['phone']
                    )
                ),
                'payment_method_data' => array(
                    'billing' => array(
                        'address' => array(
                            'line1' => $data['billingAddress']['address'],
                            'city' => $data['billingAddress']['city'],
                            'state' => $data['billingAddress']['state'],
                            'postcode' => $data['billingAddress']['zipCode'],
                            'country' => $data['billingAddress']['country']
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
                
                // Auto-confirm for Korean payment methods when only one method is selected
                $nonDirectPaymentMethods = ['apple_pay', 'google_pay','card'];
                if (count($data['paymentMethods']) === 1) {
                    $selectedMethod = $data['paymentMethods'][0];
                    if (!in_array($selectedMethod, $nonDirectPaymentMethods)) {
                        $paymentParams['confirm'] = true;
                        $this->log('Auto-confirm enabled for payment method: ' . $selectedMethod, 'info',  $data['paymentMethods'], 'payment');
                        $paymentParams['payment_method_data']['type'] = $selectedMethod;
                    }

                }
            }

            // Add card object to payment_method_data if card array exists in input
            if (!empty($data['card']) && is_array($data['card'])) {
                $cardData = array(
                    'number' => $data['card']['number'],
                    'expiry_month' => $data['card']['expiry_month'],
                    'expiry_year' => $data['card']['expiry_year'],
                    'cvc' => $data['card']['cvc'],
                    'number_type' => 'pan',
                );
                $paymentParams['payment_method_data']['card'] = $cardData;
                $this->log('Card data added to payment_method_data', 'info', $data['card'], 'payment');
                $paymentParams['payment_method_data']['type'] = $selectedMethod;
                $paymentParams['confirm'] = true;
                /**Specifies whether the funds should be requested automatically after the payment is authorized.
                 * true: The funds will be requested automatically after the payment is authorized.
                 * false: The funds will not be requested automatically after the payment is authorized.**/
                $paymentParams['auto_capture'] = true;
            }

            // Handle Apple Pay payment_method_data
            if (!empty($data['payment_method_data']) && is_array($data['payment_method_data'])) {
                $methodData = $data['payment_method_data'];
                
                if (isset($methodData['type']) && $methodData['type'] === 'apple_pay') {
                    $paymentParams['payment_method_data']['type'] = 'apple_pay';
                    
                    if (!empty($methodData['apple_pay'])) {
                        $paymentParams['payment_method_data']['apple_pay'] = array(
                            'payment' => $methodData['apple_pay']['payment'],
                            'merchant_identifier' => $methodData['apple_pay']['merchant_identifier']
                        );
                    }
                    
                    $paymentParams['confirm'] = true;
                    $paymentParams['auto_capture'] = true;
                    
                    $this->log('Apple Pay data added to payment_method_data', 'info', [
                        'type' => 'apple_pay',
                        'merchant_identifier' => $methodData['apple_pay']['merchant_identifier'] ?? 'N/A'
                    ], 'payment');
                }
            }

            // Add customer info only if email or phone is not empty
            if (!empty($data['email']) || !empty($data['phone'])) {
                $paymentParams['customer'] = array(
                    'merchant_customer_id' => 'CUST_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8),
                    'first_name' => $data['firstName'],
                    'last_name' => $data['lastName'],
                    'name' => $customerName,
                    'email' => $data['email'],
                    'phone' => $data['phone']
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

            // Prepare payment parameters based on payment_method_data from frontend
            $paymentParams = array();
            
            // Check if payment_method_data is provided
            if (isset($data['payment_method_data']) && is_array($data['payment_method_data'])) {
                $methodData = $data['payment_method_data'];
                
                // Handle card payment method
                if (isset($methodData['type']) && $methodData['type'] === 'card') {
                    if (isset($methodData['card']) && is_array($methodData['card'])) {
                        $cardData = $methodData['card'];
                        
                        // Build payment_method_data structure
                        $paymentParams['payment_method_data'] = array(
                            'type' => 'card',
                            'card' => array(
                                'number' => $cardData['number'] ?? '',
                                'expiry_month' => $cardData['expiry_month'] ?? '',
                                'expiry_year' => $cardData['expiry_year'] ?? '',
                                'cvc' => $cardData['cvc'] ?? '',
                                'number_type' => 'pan'
                            )
                        );
                        
                        // Add cardholder name if provided
                        if (!empty($cardData['name'])) {
                            $paymentParams['payment_method_data']['card']['name'] = $cardData['name'];
                        }

                    }
                } 
                // Handle Apple Pay payment method
                else if (isset($methodData['type']) && $methodData['type'] === 'apple_pay') {
                    $applePayData = $methodData['apple_pay'] ?? [];
                    
                    $paymentParams['payment_method_data'] = array(
                        'type' => 'apple_pay',
                        'apple_pay' => array(
                            'merchant_identifier' => $applePayData['merchant_identifier'] ?? '',
                            'encrypted_payment_token' => $applePayData['encrypted_payment_token'] ?? $applePayData['payment'] ?? ''
                        )
                    );
                    
                    $this->log('Apple Pay Confirm - payment_method_data', 'info', [
                        'type' => 'apple_pay',
                        'merchant_identifier' => $applePayData['merchant_identifier'] ?? 'N/A'
                    ], 'payment');
                }
                else {
                    // Handle other payment methods
                    $paymentParams['payment_method_data'] = array(
                        'type' => $methodData['type'] ?? 'unknown'
                    );
                }

            }
            
            // Add return_url if provided
            if (isset($data['return_url'])) {
                $paymentParams['return_url'] = $data['return_url'];
            }
            
            // Log confirm request
            // Log API request parameters
            $this->log('UseePay API Request - confirmPayment', 'info', $paymentParams, 'payment');

            $client = $this->getUseePayClient();

            $paymentIntent = $client->paymentIntents()->confirm(
                $paymentId,
                $paymentParams
            );
            // Log API response
            $this->log('UseePay API Response - confirmPayment', 'info', $paymentIntent, 'payment');

            $this->jsonResponse($paymentIntent);

            
        } catch (\Exception $e) {
            // Log API error
            $this->errorResponse('Payment confirmation failed: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 获取 Apple Pay 配置
     * POST /v1/payment_method_configurations
     * 用于获取 merchant_id、支持卡组织、认证方式等 Apple Pay 必要参数
     * 必填参数: currency, host, merchant_name, os_type, amount
     */
    public function getApplePayConfiguration()
    {
        global $config;
        
        try {
            $data = $this->getRequestData();
            
            // 验证必需参数
            $requiredFields = ['currency', 'host', 'merchant_name', 'os_type', 'amount'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $this->errorResponse("$field is required", 400);
                    return;
                }
            }
            
            // 准备请求参数 - 5个必填参数
            $requestParams = [
                'currency' => $data['currency'],
                'host' => $data['host'],
                'merchant_name' => $data['merchant_name'],
                'os_type' => $data['os_type'], // WEB 或 IOS
                'amount' => floatval($data['amount'])
            ];
            
            // 记录请求日志
            $this->log('Apple Pay Configuration Request', 'info', $requestParams, 'payment');
            
            // 构建 API URL
            $environment = $config['usee_pay']['environment'] === 'production' 
                ? 'https://openapi.useepay.com'
                : 'https://openapi1.uat.useepay.com';
            $apiUrl = $environment . '/api/v1/payment_method_configurations';

            $this->log('Apple Pay Configuration url', 'info', $apiUrl, 'payment');
            
            // 发送请求
            $response = $this->sendUseePayRequest($apiUrl, $requestParams);
            
            // 记录响应日志
            $this->log('Apple Pay Configuration Response', 'info', $response, 'payment');
            
            // 格式化返回数据，方便前端使用
            $formattedResponse = [
                'payment_method' => 'applepay',
                'merchant_no' => $response['merchantNo'] ?? $config['usee_pay']['merchant_no'],
                'app_id' => $response['appId'] ?? $config['usee_pay']['app_id'],
                'domain' => $response['domain'] ?? $data['host'],
                'acquire_merchant_id' => $response['apple_pay']['configuration']['merchant_identifier'] ?? null,
                'merchant_name' => $response['apple_pay']['configuration']['merchant_name'] ?? $data['merchant_name'],
                'allowed_card_networks' => $response['apple_pay']['allowed_card_networks'] ?? ['visa', 'masterCard', 'discover', 'amex'],
                'allowed_card_auth_methods' => $response['apple_pay']['allowed_card_auth_methods'] ?? ['supports3DS', 'supportsDebit', 'supportsCredit']
            ];
            
            $this->jsonResponse($formattedResponse);
            
        } catch (\Exception $e) {
            $this->log('Apple Pay Configuration Error', 'error', [
                'error' => $e->getMessage()
            ], 'payment');
            
            $this->errorResponse('Failed to get Apple Pay configuration: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Apple Pay Session 验证
     * POST /v1/payment_methods/apple_pay/validate
     * 用于向 Apple 获取有效 session
     */
    public function validateApplePaySession()
    {
        global $config;
        
        try {
            $data = $this->getRequestData();
            
            // 验证必需参数
            $requiredFields = ['displayName', 'domainName', 'merchantIdentifier', 'validationURL'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->errorResponse("$field is required", 400);
                    return;
                }
            }
            
            // 准备请求参数
            $requestParams = [
                'displayName' => $data['displayName'],
                'domainName' => $data['domainName'],
                'merchantIdentifier' => $data['merchantIdentifier'],
                'merchant_id' => $config['usee_pay']['merchant_no'],
                'validationURL' => $data['validationURL']
            ];
            
            // 记录请求日志
            $this->log('Apple Pay Session Validate Request', 'info', $requestParams, 'payment');
            
            // 构建 API URL
            $environment = $config['usee_pay']['environment'] === 'production' 
                ? 'https://openapi.useepay.com'
                : 'https://openapi1.uat.useepay.com';
            $apiUrl = $environment . '/api/v1/payment_methods/apple_pay/merchant_session';
            
            // 发送请求
            $response = $this->sendUseePayRequest($apiUrl, $requestParams);
            
            // 记录响应日志
            $this->log('Apple Pay Session Validate Response', 'info', $response, 'payment');
            
            $this->jsonResponse([
                'applePaySession' => [
                    'merchantSession' => $response['applePaySession']['merchantSession'] ?? $response
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->log('Apple Pay Session Validate Error', 'error', [
                'error' => $e->getMessage()
            ], 'payment');
            
            $this->errorResponse('Failed to validate Apple Pay session: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 发送 UseePay API 请求的通用方法
     */
    private function sendUseePayRequest($apiUrl, $requestParams)
    {
        global $config;
        
        $requestData = json_encode($requestParams);
        
        // 构建请求头
        $headers = [
            'Content-Type: application/json',
            'x-merchant-no: ' . $config['usee_pay']['merchant_no'],
            'x-api-key: ' . $config['usee_pay']['api_private_key'],
            'x-app-id: ' . $config['usee_pay']['app_id']
        ];
        
        // 使用 cURL 发送请求
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['usee_pay']['timeout']['read']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $config['usee_pay']['timeout']['connect']);
        
        // 开发环境禁用 SSL 验证
        if ($config['app']['environment'] === 'development') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new \Exception('cURL error: ' . $curlError);
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $errorMessage = isset($responseData['error']['message']) 
                ? $responseData['error']['message'] 
                : 'Unknown error (HTTP ' . $httpCode . ')';
            throw new \Exception('API error: ' . $errorMessage);
        }
        
        return $responseData;
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
