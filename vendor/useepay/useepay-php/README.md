# UseePay PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/useepay/useepay-php.svg)](https://packagist.org/packages/useepay/useepay-php)
[![PHP Version](https://img.shields.io/packagist/php-v/useepay/useepay-php.svg)](https://packagist.org/packages/useepay/useepay-php)
[![License](https://img.shields.io/packagist/l/useepay/useepay-php.svg)](https://github.com/useepay2020/useepay-php/blob/main/LICENSE)

Official UseePay PHP SDK for payment processing. Compatible with PHP 5.4+.

## Features

- 💳 Complete payment processing integration
- 👥 Customer management
- 🔐 Secure authentication with API keys
- 🌐 Support for multiple environments (Production & Sandbox)
- 📦 Easy integration with Composer
- ✅ PHP 5.4+ compatibility
- 🔒 SSL certificate auto-detection
- ⚡ Configurable timeouts and retries

## Requirements

- PHP >= 5.4.0
- cURL extension
- JSON extension
- mbstring extension

## Installation

Install via Composer:

```bash
composer require useepay/useepay-php
```

## Quick Start

### Initialize Client

```php
<?php
require_once 'vendor/autoload.php';

use UseePay\UseePayClient;
use UseePay\Model\Authentication\Authentication;
use UseePay\Net\ApiEnvironment;

// Initialize authentication
$authentication = new Authentication(
    'YOUR_MERCHANT_NO',
    'YOUR_APP_ID',
    'YOUR_API_KEY'
);

// Create client (Production)
$client = new UseePayClient($authentication);

// Or use Sandbox environment
$client = UseePayClient::withEnvironment(
    ApiEnvironment::SANDBOX,
    $authentication
);
```

### Create Payment Intent

```php
<?php
use UseePay\UseePay;

// Optional: Configure timeouts
UseePay::setConnectTimeout(30);
UseePay::setReadTimeout(60);

// Create payment
$paymentParams = array(
    'amount' => 100.00,
    'currency' => 'USD',
    'description' => 'Order #12345',
    'merchant_order_id' => 'ORDER_12345',
    'customer' => array(
        'merchant_customer_id' => 'CUST_001',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '+1234567890',
        'address' => array(
            'line1' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US'
        )
    )
);

$paymentIntent = $client->paymentIntents()->create($paymentParams);

// Handle redirect for payment
if ($paymentIntent['status'] === 'requires_payment_method') {
    $redirectUrl = $paymentIntent['next_action']['redirect']['url'];
    header('Location: ' . $redirectUrl);
    exit;
}
```

### Create Customer

```php
<?php
use UseePay\Param\Customer\CustomerCreateParams;

$params = new CustomerCreateParams();
$params->merchantCustomerId = 'CUST_' . time();
$params->firstName = 'John';
$params->lastName = 'Doe';
$params->email = 'john.doe@example.com';
$params->phone = '+1234567890';

$customer = $client->customers()->create($params);
echo "Customer ID: " . $customer['id'];
```

### Retrieve Customer

```php
<?php
$customerId = 'cus_xxxxxxxxxxxxx';
$customer = $client->customers()->retrieve($customerId);
print_r($customer);
```

### List Customers

```php
<?php
use UseePay\Param\Customer\CustomerListParams;

$params = new CustomerListParams();
$params->limit = 10;
$params->startingAfter = null;

$customers = $client->customers()->list($params);
foreach ($customers['data'] as $customer) {
    echo $customer['email'] . "\n";
}
```

## Configuration

### SSL Certificate Verification

```php
<?php
use UseePay\UseePay;

// Disable SSL verification (NOT recommended for production)
UseePay::setVerifySslCerts(false);

// Set custom CA bundle path
UseePay::setCaBundlePath('/path/to/cacert.pem');
```

### Timeout Configuration

```php
<?php
use UseePay\UseePay;

// Set connection timeout (default: 6 seconds)
UseePay::setConnectTimeout(30);

// Set read timeout (default: 30 seconds)
UseePay::setReadTimeout(60);

// Set max network retries (default: 0)
UseePay::setMaxNetworkRetries(3);
```

## Error Handling

```php
<?php
use UseePay\Exception\ApiException;
use UseePay\Exception\AuthenticationException;

try {
    $paymentIntent = $client->paymentIntents()->create($params);
} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Authentication failed: " . $e->getMessage();
} catch (ApiException $e) {
    // Handle API errors
    echo "API error: " . $e->getMessage();
    echo "Error code: " . $e->getCode();
} catch (Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}
```

## Examples

Complete examples are available in the `examples/` directory:

- **Customer Management**: `examples/CustomerExample.php`
- **Payment Checkout**: `examples/checkout.html` + `examples/checkout_handler.php`
- **E-commerce Demo**: `examples/clothing_shop.html`

## API Documentation

For detailed API documentation, visit:
- [UseePay API Documentation](https://docs-v2.useepay.com)
- [Developer Portal](https://www.useepay.com/developers)

## Support

- **Email**: technology@useepay.com
- **Issues**: [GitHub Issues](https://github.com/useepay2020/useepay-php/issues)
- **Documentation**: [API Docs](https://docs-v2.useepay.com)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the Apache License 2.0 - see the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.

---

Made with ❤️ by the UseePay Team
