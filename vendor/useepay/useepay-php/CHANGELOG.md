# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-20

### Added
- Initial release of UseePay PHP SDK
- Complete payment processing integration
- Customer management (create, retrieve, update, list)
- Payment Intent support with redirect handling
- Support for PHP 5.4+
- Automatic SSL certificate detection
- Configurable timeouts and connection settings
- Sandbox and Production environment support
- Comprehensive error handling with custom exceptions
- PSR-4 autoloading support
- Complete API documentation
- Example implementations for common use cases

### Features
- **Authentication**: Secure API key-based authentication
- **Payment Intents**: Create and manage payment intents
- **Customers**: Full CRUD operations for customer management
- **HTTP Client**: Robust HTTP client with retry logic and timeout configuration
- **SSL Support**: Automatic CA bundle detection for SSL verification
- **Environment Management**: Easy switching between sandbox and production
- **Error Handling**: Detailed exception classes for different error types

### Security
- SSL certificate verification enabled by default
- Secure API key handling
- No sensitive data logging

### Documentation
- Comprehensive README with examples
- API reference documentation
- Code examples for common scenarios
- Configuration guide

### Compatibility
- PHP 5.4.0 or higher
- cURL extension required
- JSON extension required
- mbstring extension required
