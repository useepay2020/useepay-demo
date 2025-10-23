<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

/**
 * Payment Callback Handler
 * Handles payment callback from UseePay API
 */
class PaymentCallbackHandler extends BaseController
{
    /**
     * Handle payment callback
     */
    public function handleCallback()
    {
        // Get callback parameters from URL query string
        $paymentId = isset($_GET['id']) ? $_GET['id'] : null;
        $merchantOrderId = isset($_GET['merchant_order_id']) ? $_GET['merchant_order_id'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        // Log callback request
        $this->log('Payment Callback Received', 'info', [
            'payment_id' => $paymentId,
            'merchant_order_id' => $merchantOrderId,
            'status' => $status,
            'query_params' => $_GET
        ], 'callback');

        // Validate required parameters
        if (empty($paymentId) || empty($merchantOrderId) || empty($status)) {
            $this->log('Payment Callback - Missing Parameters', 'error', [
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'status' => $status
            ], 'callback');

            // Return error response
            return $this->renderCallback([
                'success' => false,
                'message' => 'Missing required parameters',
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'status' => $status
            ]);
        }

        // Verify payment status with UseePay API
        try {
            $client = $this->getUseePayClient();
            $payment = $client->paymentIntents()->retrieve($paymentId);

            // Log verification result
            $this->log('Payment Callback - Verification', 'info', [
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'callback_status' => $status,
                'api_status' => isset($payment['status']) ? $payment['status'] : 'unknown',
                'verified' => $status === (isset($payment['status']) ? $payment['status'] : '')
            ], 'callback');

            // Determine callback result
            $callbackData = [
                'success' => $status === 'succeeded',
                'message' => $this->getStatusMessage($status),
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'status' => $status,
                'verified' => true,
                'api_status' => isset($payment['status']) ? $payment['status'] : 'unknown'
            ];

            // If payment succeeded, save order data
            if ($status === 'succeeded') {
                // TODO: Save order to database
                $this->log('Payment Callback - Success', 'info', [
                    'payment_id' => $paymentId,
                    'merchant_order_id' => $merchantOrderId,
                    'status' => $status
                ], 'callback');
            } else {
                $this->log('Payment Callback - Failed/Pending', 'warning', [
                    'payment_id' => $paymentId,
                    'merchant_order_id' => $merchantOrderId,
                    'status' => $status
                ], 'callback');
            }

            return $this->renderCallback($callbackData);

        } catch (\Exception $e) {
            $this->log('Payment Callback - Verification Error', 'error', [
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'error_message' => $e->getMessage()
            ], 'callback');

            return $this->renderCallback([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage(),
                'payment_id' => $paymentId,
                'merchant_order_id' => $merchantOrderId,
                'status' => $status,
                'verified' => false
            ]);
        }
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'succeeded' => 'Payment successful! Your order has been confirmed.',
            'processing' => 'Payment is being processed. Please wait...',
            'requires_payment_method' => 'Payment method is required.',
            'requires_action' => 'Additional action is required to complete the payment.',
            'canceled' => 'Payment has been canceled.',
            'failed' => 'Payment failed. Please try again.',
        ];

        return isset($messages[$status]) ? $messages[$status] : 'Payment status: ' . $status;
    }

    /**
     * Render callback page
     */
    private function renderCallback($data)
    {
        // Store callback data in session for display
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['payment_callback'] = $data;

        // Render callback page
        require __DIR__ . '/../Views/payment/callback.php';
    }
}
