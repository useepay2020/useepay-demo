<?php

namespace UseePay\Model\Payment;

use UseePay\Model\BaseModel;

/**
 * Refund object
 * Compatible with PHP 5.3+
 */
class Refund extends BaseModel
{
    /**
     * @var string|null Payment Intent ID
     */
    public $paymentIntentId;
    
    /**
     * @var string|null Acquirer reference number
     */
    public $acquirerReferenceNumber;
    
    /**
     * @var float|string|null Refund amount
     */
    public $amount;
    
    /**
     * @var string|null Currency
     */
    public $currency;
    
    /**
     * @var array|null Metadata key-value pairs
     */
    public $metadata;
    
    /**
     * @var string|null Refund reason
     */
    public $reason;
    
    /**
     * @var string|null Refund status
     */
    public $status;
    
    // Status constants
    const STATUS_RECEIVED = 'received';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->paymentIntentId = isset($data['payment_intent_id']) ? $data['payment_intent_id'] : null;
        $this->acquirerReferenceNumber = isset($data['acquirer_reference_number']) ? $data['acquirer_reference_number'] : null;
        $this->amount = isset($data['amount']) ? $data['amount'] : null;
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->reason = isset($data['reason']) ? $data['reason'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
    }
}
