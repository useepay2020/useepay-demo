<?php

namespace UseePay\Model\Billing;

use UseePay\Model\BaseModel;

/**
 * Invoice object
 * Compatible with PHP 5.3+
 */
class Invoice extends BaseModel
{
    public $collectionMethod;
    public $currency;
    public $customerId;
    public $description;
    public $hostedInvoiceUrl;
    public $metadata;
    public $paymentIntent;
    public $periodStart;
    public $periodEnd;
    public $subscriptionId;
    public $status;
    public $subtotal;
    public $total;
    
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_OPEN = 'open';
    const STATUS_PAID = 'paid';
    const STATUS_UNCOLLECTIBLE = 'uncollectible';
    const STATUS_VOID = 'void';
    
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->collectionMethod = isset($data['collection_method']) ? $data['collection_method'] : null;
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->customerId = isset($data['customer_id']) ? $data['customer_id'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->hostedInvoiceUrl = isset($data['hosted_invoice_url']) ? $data['hosted_invoice_url'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->paymentIntent = isset($data['payment_intent']) ? $data['payment_intent'] : null;
        $this->periodStart = isset($data['period_start']) ? $data['period_start'] : null;
        $this->periodEnd = isset($data['period_end']) ? $data['period_end'] : null;
        $this->subscriptionId = isset($data['subscription_id']) ? $data['subscription_id'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->subtotal = isset($data['subtotal']) ? $data['subtotal'] : null;
        $this->total = isset($data['total']) ? $data['total'] : null;
    }
}
