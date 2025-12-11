<?php

namespace UseePay\Param\Billing;

/**
 * Price data parameters
 * Compatible with PHP 5.3+
 */
class PriceDataParams
{
    /**
     * @var string Three-letter ISO currency code (Required)
     */
    public $currency;
    
    /**
     * @var string Product ID or Name (Required)
     */
    public $product;
    
    /**
     * @var float|string Amount to charge
     */
    public $unitAmount;
    
    /**
     * @var array|null Recurring configuration
     */
    public $recurring;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->currency !== null) $result['currency'] = $this->currency;
        if ($this->product !== null) $result['product'] = $this->product;
        if ($this->unitAmount !== null) $result['unit_amount'] = $this->unitAmount;
        if ($this->recurring !== null) $result['recurring'] = $this->recurring;
        
        return $result;
    }
}
