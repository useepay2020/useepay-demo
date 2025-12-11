<?php

namespace UseePay\Model\Payment;

use UseePay\Model\BaseModel;

/**
 * Payment Intent object
 * Compatible with PHP 5.3+
 */
class PaymentIntent extends BaseModel
{
    public $merchantOrderId;
    public $invoiceId;
    public $amount;
    public $currency;
    public $capturedAmount;
    public $status;
    public $customer;
    public $customerId;
    public $description;
    public $order;
    public $paymentMethod;
    public $paymentMethodTypes;
    public $metadata;
    public $nextAction;
    public $returnUrl;
    public $cancelUrl;
    
    // Status constants
    const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    const STATUS_REQUIRES_CUSTOMER_ACTION = 'requires_customer_action';
    const STATUS_REQUIRES_CAPTURE = 'requires_capture';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_CANCELLED = 'cancelled';
    
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->merchantOrderId = isset($data['merchant_order_id']) ? $data['merchant_order_id'] : null;
        $this->invoiceId = isset($data['invoice_id']) ? $data['invoice_id'] : null;
        $this->amount = isset($data['amount']) ? $data['amount'] : null;
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->capturedAmount = isset($data['captured_amount']) ? $data['captured_amount'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->customer = isset($data['customer']) ? $data['customer'] : null;
        $this->customerId = isset($data['customer_id']) ? $data['customer_id'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->order = isset($data['order']) ? $data['order'] : null;
        $this->paymentMethod = isset($data['payment_method']) ? $data['payment_method'] : null;
        $this->paymentMethodTypes = isset($data['payment_method_types']) ? $data['payment_method_types'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->nextAction = isset($data['next_action']) ? $data['next_action'] : null;
        $this->returnUrl = isset($data['return_url']) ? $data['return_url'] : null;
        $this->cancelUrl = isset($data['cancel_url']) ? $data['cancel_url'] : null;
    }
}
