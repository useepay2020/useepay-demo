<?php

namespace UseePay\Param\Payment;

use UseePay\Param\PageQueryParams;

/**
 * Refund query parameters
 * Compatible with PHP 5.3+
 */
class RefundQueryParams extends PageQueryParams
{
    /**
     * @var string|null Payment Intent ID filter
     */
    public $paymentIntentId;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = parent::toArray();
        
        if ($this->paymentIntentId !== null) {
            $result['payment_intent_id'] = $this->paymentIntentId;
        }
        
        return $result;
    }
}
