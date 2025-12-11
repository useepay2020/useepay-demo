<?php

namespace UseePay\Param\Customer;

use UseePay\Model\AbstractValidator;
use UseePay\Util\ValidationUtil;

/**
 * Parameters for creating a customer
 * Compatible with PHP 5.3+
 */
class CustomerCreateParams extends AbstractValidator
{
    public $name;
    public $email;
    public $merchantCustomerId;
    public $phone;
    public $firstName;
    public $lastName;
    public $description;
    public $address;
    public $metadata;
    public $shipping;
    
    /**
     * Validate parameters
     * 
     * @return void
     */
    public function validate()
    {
        ValidationUtil::rejectIfEmpty($this->merchantCustomerId, 'merchantCustomerId is required');
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->name !== null) $result['name'] = $this->name;
        if ($this->email !== null) $result['email'] = $this->email;
        if ($this->merchantCustomerId !== null) $result['merchant_customer_id'] = $this->merchantCustomerId;
        if ($this->phone !== null) $result['phone'] = $this->phone;
        if ($this->firstName !== null) $result['first_name'] = $this->firstName;
        if ($this->lastName !== null) $result['last_name'] = $this->lastName;
        if ($this->description !== null) $result['description'] = $this->description;
        if ($this->address !== null) $result['address'] = $this->address;
        if ($this->metadata !== null) $result['metadata'] = $this->metadata;
        if ($this->shipping !== null) $result['shipping'] = $this->shipping;
        
        return $result;
    }
}
