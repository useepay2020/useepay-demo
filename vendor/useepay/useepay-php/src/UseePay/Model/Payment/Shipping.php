<?php

namespace UseePay\Model\Payment;

/**
 * Shipping information
 * Compatible with PHP 5.3+
 */
class Shipping
{
    /**
     * @var Address|array|null Shipping address
     */
    public $address;
    
    /**
     * @var string|null The customer's full name or business name
     */
    public $name;
    
    /**
     * @var string|null First name of the recipient
     */
    public $firstName;
    
    /**
     * @var string|null Last name of the recipient
     */
    public $lastName;
    
    /**
     * @var string|null Phone number of the recipient
     */
    public $phone;
    
    /**
     * @var string|null Shipping method for the product
     */
    public $shippingMethod;
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->address = isset($data['address']) ? 
            (is_array($data['address']) ? new Address($data['address']) : $data['address']) : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->firstName = isset($data['first_name']) ? $data['first_name'] : null;
        $this->lastName = isset($data['last_name']) ? $data['last_name'] : null;
        $this->phone = isset($data['phone']) ? $data['phone'] : null;
        $this->shippingMethod = isset($data['shipping_method']) ? $data['shipping_method'] : null;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->address !== null) {
            $result['address'] = is_object($this->address) && method_exists($this->address, 'toArray') 
                ? $this->address->toArray() 
                : $this->address;
        }
        if ($this->name !== null) $result['name'] = $this->name;
        if ($this->firstName !== null) $result['first_name'] = $this->firstName;
        if ($this->lastName !== null) $result['last_name'] = $this->lastName;
        if ($this->phone !== null) $result['phone'] = $this->phone;
        if ($this->shippingMethod !== null) $result['shipping_method'] = $this->shippingMethod;
        
        return $result;
    }
}
