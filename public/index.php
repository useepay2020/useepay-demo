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

// Debug: log the raw values
error_log("Raw REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("Raw SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
error_log("Parsed path: " . $request);
error_log("Script directory: " . $scriptName);

// Remove script directory from request if not in document root
if (strpos($request, $scriptName) === 0) {
    $request = substr($request, strlen($scriptName));
    error_log("After removing script dir: " . $request);
}

// Remove trailing slash if present
$request = rtrim($request, '/');

// Ensure request starts with / for consistent routing
if (!empty($request) && $request[0] !== '/') {
    $request = '/' . $request;
}

error_log("After normalization: " . $request);

// Set base path for views
$basePath = '';

// Debug output (comment out in production)
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
error_log("Parsed request: " . $request);

// Simple test endpoint
if (strpos($request, '/api/') === 0) {
    error_log("API request detected: " . $request);
}

// Handle dynamic API routes with parameters
if (preg_match('#^/api/payment/confirm/([a-zA-Z0-9_-]+)$#', $request, $matches)) {
    require_once __DIR__ . '/../src/Controllers/PaymentController.php';
    $controller = new \UseePayDemo\Controllers\PaymentController();
    $controller->confirmPayment($matches[1]);
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
    case '/subscription/pricing':
        // Load subscription pricing page
        require __DIR__ . '/../src/Views/subscription/pricing.php';
        break;
    case '/subscription/checkout':
        // Load subscription checkout page
        require __DIR__ . '/../src/Views/subscription/checkout.php';
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
    case '/api/customers/create':
        require __DIR__ . '/../src/Controllers/CustomerController.php';
        $controller = new \UseePayDemo\Controllers\CustomerController();
        $controller->createCustomer();
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
