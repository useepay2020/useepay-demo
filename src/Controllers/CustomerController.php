<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Traits/Loggable.php';

use UseePayDemo\Traits\Loggable;

class CustomerController extends BaseController
{
    use Loggable;

    /**
     * Create a new customer
     */
    public function createCustomer()
    {
        // 记录请求日志
        $this->logRequest('customer');

        $data = $this->getRequestData();
        $this->log('Received customer data', 'info', $data, 'customer');

        // 验证必填字段
        $required = ['email', 'name'];
        $missing = [];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            $errorMsg = '缺少必填字段: ' . implode(', ', $missing);
            $this->log($errorMsg, 'error', ['missing_fields' => $missing], 'customer');
        }

        $this->log('Initializing UseePay client', 'info', [], 'customer');
        $client = $this->getUseePayClient();

        $customerData = [
            'email' => $data['email'],
            'name' => $data['name'] ?? '',
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'merchant_customer_id' => $data['merchantCustomerId'] ?? null,
            'country' => $data['country'] ?? 'CN', // 默认国家代码
            'metadata' => (object)$data['metadata'] ?? (object)[]
        ];
        
        // 移除空值
        $customerData = array_filter($customerData, function($value) {
            return $value !== null;
        });

        $this->log('Creating customer', 'info', $customerData, 'customer');

        try {
            $customer = $client->customers()->create($customerData);

            $this->jsonResponse($customer);

        } catch (\Exception $e) {
            $this->logException($e, 'Failed to create customer', 'customer');
            $this->errorResponse(
                'Failed to create customer: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get a customer by ID
     */
    public function getCustomer($customerId)
    {
        $this->logRequest('customer');

        try {
            $client = $this->getUseePayClient();
            $customer = $client->customers()->retrieve($customerId);

            $this->log('Retrieved customer', 'info', ['customer_id' => $customerId], 'customer');

            $this->jsonResponse($customer);

        } catch (\Exception $e) {
            $this->logException($e, 'Failed to retrieve customer', 'customer');
            $this->errorResponse(
                'Failed to retrieve customer: ' . $e->getMessage(),
                404
            );
        }
    }

}

// 处理请求
$controller = new CustomerController();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

// 路由
switch ($path) {
    case 'api/customer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->createCustomer();
        }
        break;
    case (preg_match('/^api\/customer\/([^\/]+)$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->getCustomer($matches[1]);
        }
        break;
    default:
        $controller->errorResponse('Not Found', 404);
}
