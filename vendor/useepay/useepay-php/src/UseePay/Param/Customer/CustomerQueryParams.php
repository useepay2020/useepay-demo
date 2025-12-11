<?php

namespace UseePay\Param\Customer;

use UseePay\Param\PageQueryParams;

/**
 * Parameters for querying customers
 * Compatible with PHP 5.3+
 */
class CustomerQueryParams extends PageQueryParams
{
    public $email;
    public $merchantCustomerId;
    public $phone;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = parent::toArray();
        
        if ($this->email !== null) {
            $result['email'] = $this->email;
        }
        if ($this->merchantCustomerId !== null) {
            $result['merchant_customer_id'] = $this->merchantCustomerId;
        }
        if ($this->phone !== null) {
            $result['phone'] = $this->phone;
        }
        
        return $result;
    }
}
