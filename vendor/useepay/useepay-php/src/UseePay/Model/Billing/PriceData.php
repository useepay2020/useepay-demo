<?php

namespace UseePay\Model\Billing;

/**
 * Price data for billing
 * Compatible with PHP 5.3+
 */
class PriceData
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
     * @var Recurring|array|null Recurring configuration
     */
    public $recurring;
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->product = isset($data['product']) ? $data['product'] : null;
        $this->unitAmount = isset($data['unit_amount']) ? $data['unit_amount'] : null;
        $this->recurring = isset($data['recurring']) ? 
            (is_array($data['recurring']) ? new Recurring($data['recurring']) : $data['recurring']) : null;
    }
    
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
        if ($this->recurring !== null) {
            $result['recurring'] = is_object($this->recurring) && method_exists($this->recurring, 'toArray')
                ? $this->recurring->toArray()
                : $this->recurring;
        }
        
        return $result;
    }
}
