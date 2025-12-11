<?php

namespace UseePay\Param\Payment;

use UseePay\Model\AbstractValidator;
use UseePay\Util\ValidationUtil;

/**
 * Refund creation parameters
 * Compatible with PHP 5.3+
 */
class RefundCreateParams extends AbstractValidator
{
    /**
     * @var string Payment Intent ID (Required)
     */
    public $paymentIntentId;
    
    /**
     * @var float|string Amount to refund
     */
    public $amount;
    
    /**
     * @var string Currency
     */
    public $currency;
    
    /**
     * @var array|null Metadata
     */
    public $metadata;
    
    /**
     * @var string|null Refund reason
     */
    public $reason;
    
    /**
     * Validate parameters
     * 
     * @return void
     */
    public function validate()
    {
        ValidationUtil::rejectIfEmpty($this->amount, 'amount required');
        ValidationUtil::rejectIfEmpty($this->paymentIntentId, 'paymentIntentId required');
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->paymentIntentId !== null) $result['payment_intent_id'] = $this->paymentIntentId;
        if ($this->amount !== null) $result['amount'] = $this->amount;
        if ($this->currency !== null) $result['currency'] = $this->currency;
        if ($this->metadata !== null) $result['metadata'] = $this->metadata;
        if ($this->reason !== null) $result['reason'] = $this->reason;
        
        return $result;
    }
}
