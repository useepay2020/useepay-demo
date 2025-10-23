<?php
/**
 * UseePay API Documentation - Main Entry Point
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Set error reporting based on environment
if ($config['app']['environment'] === 'development') {
    error_reporting(E_ALL);
    // For API routes, don't display errors to avoid breaking JSON responses
    // Errors will still be logged to error_log
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize UseePay SDK
use UseePay\UseePay;
use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

// Simple router
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);

// Remove script directory from request if not in document root
if (strpos($request, $scriptName) === 0) {
    $request = substr($request, strlen($scriptName));
}

// Remove trailing slash if present
$request = rtrim($request, '/');

// Set base path for views
$basePath = '';

// Debug output (comment out in production)
// error_log("Request: " . $request);
// error_log("Base path: " . $basePath);

// Debug route - helps verify routing is working
if ($request === '/debug') {
    header('Content-Type: application/json');
    echo json_encode([
        'request_uri' => $_SERVER['REQUEST_URI'],
        'script_name' => $_SERVER['SCRIPT_NAME'],
        'php_self' => $_SERVER['PHP_SELF'],
        'base_path' => $basePath,
        'request' => $request,
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'script_filename' => $_SERVER['SCRIPT_FILENAME']
    ]);
    exit;
}

// Route the request
switch ($request) {
    case '/':
    case '':
    case '/index':
        // Load home view with base path
//        $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        require __DIR__ . '/../src/Views/home.php';
        break;
            
    case '/customer':
    case '/customer/':
    case '/customer/form':
        // Load customer form
//        $basePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        require __DIR__ . '/../src/Views/customer/form.php';
        break;
            
    case '/customer/success':
        // Show success page
        require __DIR__ . '/../src/Views/customer/success.php';
        break;
            
    case '/customer/error':
        // Show error page
        require __DIR__ . '/../src/Views/customer/error.php';
        break;
    case '/payment/clothing-shop':
        // Load clothing shop page
        require_once __DIR__ . '/../src/Controllers/CheckoutController.php';
        $controller = new \UseePayDemo\Controllers\CheckoutController();
        $controller->clothingShop();
        break;
    case '/payment/checkout':
        // Load checkout page
        require_once __DIR__ . '/../src/Controllers/CheckoutController.php';
        $controller = new \UseePayDemo\Controllers\CheckoutController();
        $controller->checkout();
        break;
    case '/payment/embedded-checkout':
        // Load embedded checkout page
        require_once __DIR__ . '/../src/Controllers/CheckoutController.php';
        $controller = new \UseePayDemo\Controllers\CheckoutController();
        $controller->embeddedCheckout();
        break;
    case '/payment/api-checkout':
        // Load API checkout page
        require_once __DIR__ . '/../src/Controllers/CheckoutController.php';
        $controller = new \UseePayDemo\Controllers\CheckoutController();
        $controller->apiCheckout();
        break;
    case '/payment/callback':
        // Handle payment callback
        require_once __DIR__ . '/../src/Controllers/PaymentCallbackHandler.php';
        $controller = new \UseePayDemo\Controllers\PaymentCallbackHandler();
        $controller->handleCallback();
        break;
    case '/api/payment':
        require __DIR__ . '/../src/Controllers/PaymentApiHandler.php';
        break;
    case '/api/customer':
        require __DIR__ . '/../src/Controllers/CustomerController.php';
        break;
    case '/api/subscription':
        require __DIR__ . '/../src/Controllers/SubscriptionController.php';
        break;
    case '/api/webhook':
        break;
    case '/docs':
    case '/api-docs':
        header('Location: /useepay-demo/docs/index.html');
        exit;
    default:
        http_response_code(404);
        // Make sure basePath is available to the 404 view
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        require __DIR__ . '/../src/Views/404.php';
        break;
}
