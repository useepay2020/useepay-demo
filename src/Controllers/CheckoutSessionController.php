<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

/**
 * Checkout Session Controller
 * 
 * Handles Checkout Session API operations for both one-time payments and subscriptions.
 * API Endpoint: POST /api/v1/checkout_sessions
 */
class CheckoutSessionController extends BaseController
{
    /**
     * Create a Checkout Session
     * 
     * Supports two modes:
     * - payment: One-time payment
     * - subscription: Recurring subscription payment
     */
    public function createCheckoutSession()
    {
        global $config;
        
        $data = $this->getRequestData();
        
        // Log complete request data for debugging
        $this->log('Complete Request Data - createCheckoutSession', 'info', $data, 'checkout_session');
        
        // Validate required fields
        $requiredFields = ['items', 'totals'];
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            $this->errorResponse('Missing required fields: ' . implode(', ', $missing), 400);
            return;
        }
        
        // Validate mode
        $validModes = ['payment', 'subscription'];
        if (!in_array($data['mode'], $validModes)) {
            $this->errorResponse('Invalid mode. Must be one of: ' . implode(', ', $validModes), 400);
            return;
        }
        
        // Validate items
        if (!is_array($data['items']) || count($data['items']) === 0) {
            $this->errorResponse('items must be a non-empty array', 400);
            return;
        }
        
        // Validate subscription mode requirements
        if ($data['mode'] === 'subscription') {
            // Check if line_items have recurring configuration
            foreach ($data['items'] as $index => $item) {
                if (!isset($item['price_data']['recurring'])) {
                    $this->errorResponse("items[$index].price_data.recurring is required for subscription mode", 400);
                    return;
                }
            }
        }
        
        try {
            // Generate unique merchant order ID if not provided
            $merchantOrderId = $data['merchant_order_id'] ?? ('CS_' . time() . '_' . rand(1000, 9999));
            
            // Prepare checkout session parameters
            $sessionParams = $this->buildCheckoutSessionParams($data, $merchantOrderId);
            
            // Log API request parameters
            $this->log('UseePay API Request - createCheckoutSession', 'info', $sessionParams, 'checkout_session');
            
            // Create checkout session via SDK
            $client = $this->getUseePayClient();
            $checkoutSession = $client->checkoutSessions()->create($sessionParams);
            
            // Log API response
            $this->log('UseePay API Response - createCheckoutSession', 'info', $checkoutSession, 'checkout_session');
            
            $this->jsonResponse($checkoutSession);
            
        } catch (\Exception $e) {
            $this->log('UseePay API Error - createCheckoutSession', 'error', [
                'error' => $e->getMessage()
            ], 'checkout_session');
            
            $this->errorResponse('Checkout session creation failed: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Build checkout session parameters from request data
     * 
     * @param array $data Request data
     * @param string $merchantOrderId Merchant order ID
     * @return array Checkout session parameters
     */
    private function buildCheckoutSessionParams(array $data, string $merchantOrderId): array
    {
        global $config;
        
        $params = [
            'mode' => $data['mode'],
            'ui_mode' => $data['ui_mode'] ?? 'custom',
            'amount' => floatval($data['totals']['totalAmount']),
            'currency' => strtoupper($data['totals']['currency']),
            'merchant_order_id' => $merchantOrderId,
            'line_items' => $this->buildLineItems($data['items'], $data['totals']['currency']),
        ];
        
        // Add billing information (required)
        if (isset($data['billing'])) {
            $params['billing'] = $this->buildBillingInfo($data['billing']);
        } elseif (isset($data['firstName']) && isset($data['lastName']) && isset($data['email'])) {
            // Build billing from flat fields for backward compatibility
            $params['billing'] = [
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];
            
            // Add billing address if available
            if (isset($data['billingAddress'])) {
                $params['billing']['address'] = [
                    'line1' => $data['billingAddress']['address'] ?? $data['billingAddress']['line1'] ?? '',
                    'line2' => $data['billingAddress']['line2'] ?? '',
                    'city' => $data['billingAddress']['city'] ?? '',
                    'state' => $data['billingAddress']['state'] ?? '',
                    'postcode' => $data['billingAddress']['zipCode'] ?? $data['billingAddress']['postcode'] ?? '',
                    'country' => $data['billingAddress']['country'] ?? '',
                ];
            }
        }
        
        // Add customer information
        if (isset($data['customer_id'])) {
            $params['customer_id'] = $data['customer_id'];
        } elseif (isset($data['customer'])) {
            $params['customer'] = $this->buildCustomerInfo($data['customer']);
        } elseif (isset($data['email']) || isset($data['phone'])) {
            // Build customer from flat fields
            $params['customer'] = [
                'merchant_customer_id' => $data['merchant_customer_id'] ?? ('CUST_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8)),
                'name' => trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ];
        }
        
        // Add payment method types
        if (isset($data['payment_method_types']) && is_array($data['payment_method_types'])) {
            $params['payment_method_types'] = $data['payment_method_types'];
        } elseif (isset($data['paymentMethods']) && is_array($data['paymentMethods'])) {
            $params['payment_method_types'] = $data['paymentMethods'];
        }
        
        // Add subscription data for subscription mode
        if ($data['mode'] === 'subscription') {
            $params['subscription_data'] = $this->buildSubscriptionData($data['subscription_data'] ?? []);
            $params['collection_method'] = $data['collection_method'] ?? 'auto_charge';
        }
        
        // Add shipping information if provided
        if (isset($data['shipping'])) {
            $params['shipping'] = $this->buildShippingInfo($data['shipping']);
        } elseif (isset($data['shippingAddress'])) {
            $params['shipping'] = [
                'name' => trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')),
                'phone' => $data['phone'] ?? null,
                'address' => [
                    'line1' => $data['shippingAddress']['address'] ?? $data['shippingAddress']['line1'] ?? '',
                    'line2' => $data['shippingAddress']['line2'] ?? '',
                    'city' => $data['shippingAddress']['city'] ?? '',
                    'state' => $data['shippingAddress']['state'] ?? '',
                    'postcode' => $data['shippingAddress']['zipCode'] ?? $data['shippingAddress']['postcode'] ?? '',
                    'country' => $data['shippingAddress']['country'] ?? '',
                ],
            ];
        }
        
        // Add metadata if provided
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $params['metadata'] = $data['metadata'];
        }
        
        // Add return URL
//        if (isset($data['return_url'])) {
//            $params['return_url'] = $data['return_url'];
//        } else {
//            $params['return_url'] = $config['usee_pay']['callback_url'] ?? null;
//        }
        
        return $params;
    }
    
    /**
     * Build line items array
     * 
     * @param array $items Line items from request
     * @param string $currency Default currency
     * @return array Formatted line items
     */
    private function buildLineItems(array $items, string $currency): array
    {
        $lineItems = [];
        
        foreach ($items as $item) {
            $lineItem = [
                'quantity' => intval($item['quantity'] ?? 1),
                'price_data' => [
                    'product_data' => [
                        'name' => $item['price_data']['product_data']['name'] ?? $item['name'] ?? 'Product',
                        'desc' => $item['price_data']['product_data']['desc'] ?? $item['description'] ?? '',
                    ],
                    'unit_amount' => floatval($item['price_data']['unit_amount'] ?? $item['price'] ?? 0),
                    'currency' => strtoupper($item['price_data']['currency'] ?? $currency),
                ],
            ];
            
            // Add recurring configuration for subscription mode
            if (isset($item['price_data']['recurring'])) {
                $recurring = $item['price_data']['recurring'];
                $lineItem['price_data']['recurring'] = [
                    'interval' => $recurring['interval'] ?? 'month',
                    'interval_count' => intval($recurring['interval_count'] ?? 1),
                    'unit_amount' => $recurring['unit_amount'] ?? $lineItem['price_data']['unit_amount'],
                ];
                
                if (isset($recurring['total_billing_cycles'])) {
                    $lineItem['price_data']['recurring']['total_billing_cycles'] = intval($recurring['total_billing_cycles']);
                }
            }
            
            $lineItems[] = $lineItem;
        }
        
        return $lineItems;
    }
    
    /**
     * Build customer information
     * 
     * @param array $customer Customer data
     * @return array Formatted customer info
     */
    private function buildCustomerInfo(array $customer): array
    {
        return [
            'merchant_customer_id' => $customer['merchant_customer_id'] ?? ('CUST_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8)),
            'name' => $customer['name'] ?? '',
            'email' => $customer['email'] ?? null,
            'phone' => $customer['phone'] ?? null,
        ];
    }
    
    /**
     * Build billing information
     * 
     * @param array $billing Billing data
     * @return array Formatted billing info
     */
    private function buildBillingInfo(array $billing): array
    {
        $billingInfo = [
            'first_name' => $billing['first_name'] ?? '',
            'last_name' => $billing['last_name'] ?? '',
            'email' => $billing['email'] ?? '',
        ];
        
        if (isset($billing['phone'])) {
            $billingInfo['phone'] = $billing['phone'];
        }
        
        if (isset($billing['date_of_birth'])) {
            $billingInfo['date_of_birth'] = $billing['date_of_birth'];
        }
        
        if (isset($billing['address'])) {
            $billingInfo['address'] = $this->buildAddress($billing['address']);
        }
        
        return $billingInfo;
    }
    
    /**
     * Build shipping information
     * 
     * @param array $shipping Shipping data
     * @return array Formatted shipping info
     */
    private function buildShippingInfo(array $shipping): array
    {
        $shippingInfo = [
            'name' => $shipping['name'] ?? '',
        ];
        
        if (isset($shipping['phone'])) {
            $shippingInfo['phone'] = $shipping['phone'];
        }
        
        if (isset($shipping['address'])) {
            $shippingInfo['address'] = $this->buildAddress($shipping['address']);
        }
        
        return $shippingInfo;
    }
    
    /**
     * Build address information
     * 
     * @param array $address Address data
     * @return array Formatted address
     */
    private function buildAddress(array $address): array
    {
        return [
            'line1' => $address['line1'] ?? '',
            'line2' => $address['line2'] ?? '',
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'postcode' => $address['postcode'] ?? '',
            'country' => $address['country'] ?? '',
        ];
    }
    
    /**
     * Build subscription data
     * 
     * @param array $subscriptionData Subscription configuration
     * @return array Formatted subscription data
     */
    private function buildSubscriptionData(array $subscriptionData): array
    {
        $result = [];
        
        // Add discount period configuration if provided
        if (isset($subscriptionData['discount_period_config'])) {
            $discountConfig = $subscriptionData['discount_period_config'];
            $result['discount_period_config'] = [
                'discount_period_count' => intval($discountConfig['discount_period_count'] ?? 1),
                'discount_period_amount' => floatval($discountConfig['discount_period_amount'] ?? 0),
            ];
        }
        
        return $result;
    }
    
    /**
     * Retrieve a Checkout Session
     * 
     * @param string $sessionId Checkout Session ID
     */
    public function getCheckoutSession($sessionId)
    {
        if (empty($sessionId)) {
            $this->errorResponse('Checkout Session ID is required', 400);
            return;
        }
        
        try {
            // Log API request
            $this->log('UseePay API Request - getCheckoutSession', 'info', [
                'method' => 'checkoutSessions.retrieve',
                'session_id' => $sessionId
            ], 'checkout_session');
            
            $client = $this->getUseePayClient();
            $checkoutSession = $client->checkoutSessions()->retrieve($sessionId);
            
            // Log API response
            $this->log('UseePay API Response - getCheckoutSession', 'info', [
                'method' => 'checkoutSessions.retrieve',
                'status' => 'success',
                'session_id' => $sessionId,
                'session_status' => $checkoutSession['status'] ?? 'N/A'
            ], 'checkout_session');
            
            $this->jsonResponse([
                'checkout_session' => $checkoutSession
            ]);
            
        } catch (\Exception $e) {
            // Log API error
            $this->log('UseePay API Error - getCheckoutSession', 'error', [
                'method' => 'checkoutSessions.retrieve',
                'status' => 'failed',
                'session_id' => $sessionId,
                'error_message' => $e->getMessage()
            ], 'checkout_session');
            
            $this->errorResponse('Failed to retrieve checkout session: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Expire a Checkout Session
     * 
     * @param string $sessionId Checkout Session ID
     */
    public function expireCheckoutSession($sessionId)
    {
        if (empty($sessionId)) {
            $this->errorResponse('Checkout Session ID is required', 400);
            return;
        }
        
        try {
            // Log API request
            $this->log('UseePay API Request - expireCheckoutSession', 'info', [
                'method' => 'checkoutSessions.expire',
                'session_id' => $sessionId
            ], 'checkout_session');
            
            $client = $this->getUseePayClient();
            $result = $client->checkoutSessions()->expire($sessionId);
            
            // Log API response
            $this->log('UseePay API Response - expireCheckoutSession', 'info', [
                'method' => 'checkoutSessions.expire',
                'status' => 'success',
                'session_id' => $sessionId
            ], 'checkout_session');
            
            $this->jsonResponse([
                'expired' => true,
                'session_id' => $sessionId,
                'details' => $result
            ]);
            
        } catch (\Exception $e) {
            // Log API error
            $this->log('UseePay API Error - expireCheckoutSession', 'error', [
                'method' => 'checkoutSessions.expire',
                'status' => 'failed',
                'session_id' => $sessionId,
                'error_message' => $e->getMessage()
            ], 'checkout_session');
            
            $this->errorResponse('Failed to expire checkout session: ' . $e->getMessage(), 500);
        }
    }
    
}
