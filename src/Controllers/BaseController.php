<?php

namespace UseePayDemo\Controllers;

use UseePay\UseePay;
use UseePayDemo\Traits\Loggable;

require_once __DIR__ . '/../Traits/Loggable.php';

/**
 * Base controller class for handling common functionality
 */
class BaseController
{
    use Loggable;
    /**
     * Send JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        // 清除任何之前的输出缓冲
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $data,
            'timestamp' => date('c')
        ];
        
        // 记录响应日志
        $this->log('Sending JSON response', 'info', [
            'status_code' => $statusCode,
            'response' => $response
        ], 'response');
        
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send error response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return void
     */
    public function errorResponse($message, $statusCode = 400, $errors = [])
    {
        $response = [
            'error' => [
                'message' => $message,
                'code' => $statusCode,
            ]
        ];

        if (!empty($errors)) {
            $response['error']['details'] = $errors;
        }
        
        // 记录错误响应日志
        $this->log('Sending error response', 'error', [
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors
        ], 'error');

        $this->jsonResponse($response, $statusCode);
    }

    /**
     * Get request data from input
     *
     * @return array
     */
    protected function getRequestData()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = $_POST;
        }

        return $data;
    }

    /**
     * Get UseePay client instance
     *
     * @return \UseePay\UseePayClient
     */
    protected function getUseePayClient()
    {
        global $config;
        
        $authentication = new \UseePay\Model\Authentication\Authentication(
            $config['usee_pay']['merchant_no'],
            $config['usee_pay']['app_id'],
            $config['usee_pay']['api_private_key']
        );

        $environment = $config['usee_pay']['environment'] === 'production' 
            ? \UseePay\Net\ApiEnvironment::PRODUCTION 
            : \UseePay\Net\ApiEnvironment::SANDBOX;
            
        // Set timeouts from config
        $timeoutConfig = $config['usee_pay']['timeout'];
        UseePay::setConnectTimeout($timeoutConfig['connect']);  // Connection timeout in seconds
        UseePay::setReadTimeout($timeoutConfig['read']);       // Read timeout in seconds

        $client = \UseePay\UseePayClient::withEnvironment($environment, $authentication);
        
        // Disable SSL verification in development
        if ($config['app']['environment'] === 'development') {
            \UseePay\UseePay::setVerifySslCerts(false);
            $this->log('SSL verification disabled for development', 'info', [], 'api');
        }
        
        $this->log('UseePay client initialized successfully', 'info', [], 'api');

        return $client;
    }
}
