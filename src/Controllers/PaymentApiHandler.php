<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/PaymentController.php';

/**
 * Payment API Handler
 * Handles API requests for payment operations
 */
class PaymentApiHandler
{
    private $controller;

    public function __construct()
    {
        $this->controller = new PaymentController();
    }

    /**
     * Handle API requests
     */
    public function handle()
    {
        // Check if this is an API checkout request
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        $endpoint = end($pathParts);

        // Route the request
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (is_numeric($endpoint)) {
                    if (strpos($path, '/confirm') !== false) {
                        $this->controller->confirmPayment($endpoint);
                    } else if (strpos($path, '/cancel') !== false) {
                        $this->controller->cancelPayment($endpoint);
                    } else {
                        $this->controller->createPayment();
                    }
                } else {
                    $this->controller->createPayment();
                }
                break;
            case 'GET':
                if (is_numeric($endpoint)) {
                    $this->controller->getPayment($endpoint);
                } else {
                    $this->controller->errorResponse('Invalid payment ID', 400);
                }
                break;
            default:
                $this->controller->errorResponse('Method not allowed', 405);
                break;
        }
    }
}

// Handle the API request
$handler = new PaymentApiHandler();
$handler->handle();
