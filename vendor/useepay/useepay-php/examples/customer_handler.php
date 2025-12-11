<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;
use UseePay\Param\Customer\CustomerCreateParams;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        'success' => false,
        'message' => 'Method not allowed'
    ));
    exit;
}

try {
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $merchantCustomerId = isset($_POST['merchantCustomerId']) ? trim($_POST['merchantCustomerId']) : '';

    // Validate required fields
    if (empty($name)) {
        throw new Exception('客户姓名不能为空');
    }
    if (empty($email)) {
        throw new Exception('电子邮箱不能为空');
    }
    if (empty($phone)) {
        throw new Exception('电话号码不能为空');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('电子邮箱格式不正确');
    }

    // Initialize authentication (same as CustomerExample.php)
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

    // Create customer parameters
    $params = new CustomerCreateParams();
    $params->name = $name;
    $params->email = $email;
    $params->phone = $phone;
    
    // If merchantCustomerId is not provided, generate one
    if (empty($merchantCustomerId)) {
        $params->merchantCustomerId = 'CUST_' . time();
    } else {
        $params->merchantCustomerId = $merchantCustomerId;
    }

    // Create customer (using the same logic as CustomerExample.php)
    $customer = $client->customers()->create($params);

    // Return success response
    echo json_encode(array(
        'success' => true,
        'message' => '客户创建成功',
        'data' => array(
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'merchantCustomerId' => $customer->merchantCustomerId
        )
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
