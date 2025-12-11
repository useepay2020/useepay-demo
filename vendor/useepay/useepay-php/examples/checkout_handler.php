<?php
/**
 * Checkout Handler - Process checkout and create payment
 */

// 日志函数
function logMessage($message, $data = null) {
    $logDir = dirname(__FILE__) . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/checkout_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    if ($data !== null) {
        if (is_array($data) || is_object($data)) {
            $logMessage .= 'Data: ' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        } else {
            $logMessage .= 'Data: ' . $data . PHP_EOL;
        }
    }
    
    $logMessage .= str_repeat('-', 80) . PHP_EOL;
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

// Enable error reporting for debugging (but don't display errors in output)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);     // Log errors instead
// Start output buffering to catch any unexpected output
ob_start();

try {
    // 记录请求开始
    logMessage('=== 开始处理支付请求 ===');
    logMessage('请求方法: ' . $_SERVER['REQUEST_METHOD']);
    logMessage('请求头:', getallheaders());
    logMessage('POST 数据:', $_POST);
    logMessage('GET 参数:', $_GET);
    
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = array('firstName', 'lastName', 'email', 'address', 'city', 'state', 'zipCode', 'country', 'phone', 'paymentMethod', 'items', 'totals');
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Validate items
    if (!is_array($data['items']) || count($data['items']) === 0) {
        throw new Exception('Cart is empty');
    }

    // Initialize UseePay authentication
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

    // Set explicit timeouts (in seconds)
    UseePay::setConnectTimeout(30);  // 30 seconds for connection
    UseePay::setReadTimeout(60);     // 60 seconds for reading response

    // Disable SSL verification for development (remove in production)
    UseePay::setVerifySslCerts(false);

    // Generate unique order ID
    $orderId = 'ORD_' . time() . '_' . rand(1000, 9999);

    // Convert total amount to cents (USD)
    $totalAmount = floatval($data['totals']['total']);

    // Prepare customer information
    $customerName = $data['firstName'] . ' ' . $data['lastName'];
    $shippingAddress = $data['address'] . ', ' . $data['city'] . ', ' . $data['state'] . ' ' . $data['zipCode'] . ', ' . $data['country'];

    // Prepare items description
    $itemsDescription = array();
    foreach ($data['items'] as $item) {
        $itemsDescription[] = $item['name'] . ' x' . $item['quantity'];
    }
    $description = 'Order: ' . implode(', ', $itemsDescription);

    // Prepare order items
    $orderItems = array_map(function($item) {
        return [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'currency' => 'USD',
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
    $browserInfo = get_browser(null, true);
    
    // Prepare device data
    $deviceData = array(
        'ip_address' => $ipAddress,
        'user_agent' => $userAgent,
        'browser' => array(
            'browser' => isset($browserInfo['browser']) ? $browserInfo['browser'] : 'Unknown',
            'version' => isset($browserInfo['version']) ? $browserInfo['version'] : 'Unknown',
            'platform' => isset($browserInfo['platform']) ? $browserInfo['platform'] : 'Unknown',
            'device_type' => isset($browserInfo['device_type']) ? $browserInfo['device_type'] : 'Unknown',
            'is_mobile' => isset($browserInfo['ismobiledevice']) ? $browserInfo['ismobiledevice'] : false,
            'is_tablet' => isset($browserInfo['istablet']) ? $browserInfo['istablet'] : false,
            'is_crawler' => isset($browserInfo['crawler']) ? $browserInfo['crawler'] : false
        ),
        'accept_language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
        'request_time' => date('Y-m-d H:i:s'),
        'request_method' => $_SERVER['REQUEST_METHOD']
    );

    // 记录订单信息
    logMessage('创建支付参数', [
        'order_id' => $orderId,
        'amount' => $totalAmount,
        'customer_email' => $data['email'],
        'customer_name' => $customerName
    ]);
    
    // Create payment intent parameters
    $paymentParams = array(
        'amount' => $totalAmount,
        'currency' => 'USD',
        'description' => $description,
        'merchant_order_id' => $orderId,
        'device_data' => $deviceData,
        'customer' => array(
            //TODO: 需要把这块校验拿掉
            'merchant_customer_id' => 'CUST_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8),
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => array(
                'line1' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['zipCode'],
                'country' => $data['country']
            )
        ),
        'order' => array(
            'products' => $orderItems,
            'shipping_address' => array(
                'line1' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['zipCode'],
                'country' => $data['country']
            )
        ),

    );

    // 记录支付参数（不记录敏感信息）
    $logPaymentParams = $paymentParams;
    if (isset($logPaymentParams['card'])) {
        unset($logPaymentParams['card']);
    }
    logMessage('发送支付请求', $logPaymentParams);
    
    // Create payment intent
    $paymentIntent = $client->paymentIntents()->create($paymentParams);
    logMessage('支付请求响应', $paymentIntent);

    // Check if payment intent was created successfully
    if (!isset($paymentIntent['id'])) {
        throw new Exception('Failed to create payment intent');
    }

    // Handle requires_payment_method status with redirect
    $paymentStatus = isset($paymentIntent['status']) ? $paymentIntent['status'] : 'unknown';
    logMessage("支付状态: $paymentStatus");

//    if ($paymentStatus === 'requires_payment_method') {
//        logMessage('需要支付方法，准备重定向到支付页面');
//        if (isset($paymentIntent['next_action']['redirect']['url'])) {
//            $redirectUrl = $paymentIntent['next_action']['redirect']['url'];
//            logMessage("重定向到支付页面: $redirectUrl");
//
//            // 302 Redirect to the payment URL
//            //header('Location: ' . $redirectUrl, true, 302);
//            echo '<script>window.location.href="' . htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8') . '";</script>';
//
//            // 记录重定向日志
//            logMessage('执行重定向', ['url' => $redirectUrl]);
//            exit;
//        } else if (isset($paymentIntent['next_action']['redirect_to_url'])) {
//            // Handle case where the URL might be directly under redirect_to_url
//            header('Location: ' . $paymentIntent['next_action']['redirect_to_url'], true, 302);
//            exit;
//        }
//    }

    // Prepare response for non-redirect cases
    $response = array(
        'success' => true,
        'message' => 'Payment intent created successfully',
        'data' => array(
            'order_id' => $orderId,
            'payment_intent_id' => $paymentIntent['id'],
            'amount' => isset($paymentIntent['amount']) ? $paymentIntent['amount'] : $totalAmount,
            'currency' => isset($paymentIntent['currency']) ? $paymentIntent['currency'] : 'USD',
            'status' => isset($paymentIntent['status']) ? $paymentIntent['status'] : 'pending',
            'customer_name' => $customerName,
            'customer_email' => $data['email'],
            'created_at' => date('Y-m-d H:i:s'),
            'next_action' => isset($paymentIntent['next_action']) ? $paymentIntent['next_action'] : null
        )
    );

    // Log the transaction
    logMessage('支付成功', [
        'order_id' => $orderId,
        'payment_intent_id' => $paymentIntent['id'],
        'amount' => $totalAmount,
        'currency' => isset($paymentIntent['currency']) ? $paymentIntent['currency'] : 'USD',
        'status' => $paymentStatus
    ]);
    
    $logData = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'order_id' => $orderId,
        'payment_intent_id' => $paymentIntent['id'],
        'amount' => $totalAmount,
        'customer' => $customerName,
        'email' => $data['email']
    );
    
    // You can save this to a database or log file
    // file_put_contents(__DIR__ . '/logs/transactions.log', json_encode($logData) . "\n", FILE_APPEND);

    // Clear any unexpected output before sending JSON
    ob_clean();
    echo json_encode($response);

} catch (Exception $e) {
    // 记录异常信息
    logMessage('处理支付时发生异常', [
        'message' => $e->getMessage(),
        'file' => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    // 获取请求头（PHP 5.x 兼容方式）
    $headers = array();
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            }
        }
    }

    // 准备错误详情
    $errorDetails = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => array(
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ),
        'request' => array(
            'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI',
            'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            'headers' => $headers,
            'post_data' => $_POST,
            'raw_input' => file_get_contents('php://input'),
            'get_params' => $_GET
        ),
        'environment' => array(
            'php_version' => phpversion(),
            'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '',
            'remote_addr' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'http_referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
        )
    );

    // 确保日志目录存在
    $logDir = dirname(__FILE__) . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // 记录错误日志
    $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';
    @file_put_contents($logFile, json_encode($errorDetails, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0) . "\n\n", FILE_APPEND);

    // Clear any unexpected output before sending JSON
    ob_clean();
    
    // 设置响应头
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);

    // 判断是否本地环境
    $isLocal = (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1', '::1'))) ||
        (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')));

    // 构建响应数据
    $response = array(
        'success' => false,
        'error' => array(
            'message' => $e->getMessage()
        )
    );

    // 开发环境下显示更多调试信息
    if ($isLocal) {
        $response['error']['file'] = $e->getFile() . ':' . $e->getLine();
        $response['debug'] = array(
            'exception' => get_class($e),
            'trace' => $e->getTrace(),
            'request_data' => array(
                'post' => $_POST,
                'get' => $_GET,
                'input' => file_get_contents('php://input')
            )
        );
    }

    // 输出JSON响应
    echo defined('JSON_PRETTY_PRINT')
        ? json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        : json_encode($response);
}