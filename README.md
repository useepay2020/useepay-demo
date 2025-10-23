# UseePay API Demo

This is a PHP-based API documentation and demo project for the UseePay payment gateway. It provides a complete implementation of the UseePay API with examples for payments, subscriptions, and webhook handling.

## Features

- **Payment Processing**: Create, retrieve, confirm, and cancel payments
- **Customer Management**: Create, retrieve, update, and delete customer records
- **Subscription Management**: Handle recurring billing with subscriptions
- **Webhook Handling**: Process and respond to UseePay webhook events
- **RESTful API**: Clean, consistent API endpoints following REST principles

## Requirements

- PHP 7.4 or higher
- Composer (for dependency management)
- Web server (Apache/Nginx) with PHP support
- UseePay API credentials (API Key, Merchant ID, etc.)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/useepay-demo.git
   cd useepay-demo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the example configuration file and update with your credentials:
   ```bash
   cp config/config.example.php config/config.php
   ```
   Then edit `config/config.php` with your UseePay API credentials.

4. Configure your web server to point to the `public` directory.

## Configuration

Create a `.env` file in the project root with the following variables:

```
APP_ENV=development
APP_DEBUG=true

# UseePay API Credentials
USEEPAY_PRIVATE_API_KEY=your_private_api_key_here
USEEPAY_PUBLIC_API_KEY=your_public_api_key_here
USEEPAY_MERCHANT_NO=your_merchant_no_here
USEEPAY_APP_ID=your_app_id_here
USEEPAY_ENV=sandbox  # or 'production' for live environment
USEEPAY_CALLBACK_URL=your_callback_url_here

```

## API Endpoints

### Payments

- `POST /api/payment` - Create a new payment
- `GET /api/payment/{id}` - Retrieve a payment
- `POST /api/payment/{id}/confirm` - Confirm a payment
- `POST /api/payment/{id}/cancel` - Cancel a payment

### Customers

- `GET /api/customer` - List customers
- `POST /api/customer` - Create a new customer
- `GET /api/customer/{id}` - Retrieve a customer
- `PUT /api/customer/{id}` - Update a customer
- `DELETE /api/customer/{id}` - Delete a customer

### Subscriptions

- `GET /api/subscription` - List subscriptions
- `POST /api/subscription` - Create a new subscription
- `GET /api/subscription/{id}` - Retrieve a subscription
- `PUT /api/subscription/{id}` - Update a subscription
- `DELETE /api/subscription/{id}` - Cancel a subscription

### Webhooks

- `POST /api/webhook` - Handle UseePay webhook events

## Webhook Setup

1. Configure your UseePay dashboard to send webhooks to your endpoint (e.g., `https://yourdomain.com/api/webhook`)
2. Set the webhook secret in your `.env` file:
   ```
   USEEPAY_WEBHOOK_SECRET=your_webhook_secret_here
   ```
3. The webhook handler will automatically verify signatures and process events

## Testing

You can test the API using tools like cURL or Postman. Here's an example cURL command to create a payment:

```bash
curl -X POST https://yourdomain.com/api/payment \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1000,
    "currency": "USD",
    "description": "Test Payment",
    "merchant_order_id": "ORDER_'$(date +%s)'"
  }'
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please contact UseePay support at support@useepay.com.
