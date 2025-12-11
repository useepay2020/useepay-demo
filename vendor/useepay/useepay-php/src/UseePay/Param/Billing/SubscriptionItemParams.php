<?php

namespace UseePay\Param\Billing;

/**
 * Subscription item parameters
 * Compatible with PHP 5.3+
 */
class SubscriptionItemParams
{
    /**
     * @var array|null Metadata key-value pairs
     */
    public $metadata;
    
    /**
     * @var int|null Quantity for this item
     */
    public $quantity;
    
    /**
     * @var array|null Price data
     */
    public $priceData;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->metadata !== null) $result['metadata'] = $this->metadata;
        if ($this->quantity !== null) $result['quantity'] = $this->quantity;
        if ($this->priceData !== null) $result['price_data'] = $this->priceData;
        
        return $result;
    }
}
