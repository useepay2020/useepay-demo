<?php

namespace UseePay\Model\Billing;

use UseePay\Model\BaseModel;

/**
 * Subscription object
 * Compatible with PHP 5.3+
 */
class Subscription extends BaseModel
{
    public $customerId;
    public $recurring;
    public $cancelAtPeriodEnd;
    public $currency;
    public $currentPeriodEnd;
    public $currentPeriodStart;
    public $description;
    public $order;
    public $latestInvoice;
    public $firstInvoiceId;
    public $metadata;
    public $status;
    public $canceledAt;
    public $cancellationDetails;
    
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_PAUSED = 'paused';
    const STATUS_TRIALING = 'trialing';
    const STATUS_UNPAID = 'unpaid';
    
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->customerId = isset($data['customer_id']) ? $data['customer_id'] : null;
        $this->recurring = isset($data['recurring']) ? 
            (is_array($data['recurring']) ? new Recurring($data['recurring']) : $data['recurring']) : null;
        $this->cancelAtPeriodEnd = isset($data['cancel_at_period_end']) ? $data['cancel_at_period_end'] : null;
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->currentPeriodEnd = isset($data['current_period_end']) ? $data['current_period_end'] : null;
        $this->currentPeriodStart = isset($data['current_period_start']) ? $data['current_period_start'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->order = isset($data['order']) ? $data['order'] : null;
        $this->latestInvoice = isset($data['latest_invoice']) ? $data['latest_invoice'] : null;
        $this->firstInvoiceId = isset($data['first_invoice_id']) ? $data['first_invoice_id'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->canceledAt = isset($data['canceled_at']) ? $data['canceled_at'] : null;
        $this->cancellationDetails = isset($data['cancellation_details']) ? 
            (is_array($data['cancellation_details']) ? new CancellationDetails($data['cancellation_details']) : $data['cancellation_details']) : null;
    }
}
