<?php

namespace UseePay\Model\Payment;

/**
 * Address object
 * Compatible with PHP 5.3+
 */
class Address
{
    /**
     * @var string|null Country code (ISO 3166-1 alpha-2) - Required
     */
    public $country;
    
    /**
     * @var string|null City of the address
     */
    public $city;
    
    /**
     * @var string|null Postcode of the address
     */
    public $postcode;
    
    /**
     * @var string|null State or province of the address
     */
    public $state;
    
    /**
     * @var string|null Address line 1
     */
    public $line1;
    
    /**
     * @var string|null Address line 2
     */
    public $line2;
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->country = isset($data['country']) ? $data['country'] : null;
        $this->city = isset($data['city']) ? $data['city'] : null;
        $this->postcode = isset($data['postcode']) ? $data['postcode'] : null;
        $this->state = isset($data['state']) ? $data['state'] : null;
        $this->line1 = isset($data['line1']) ? $data['line1'] : null;
        $this->line2 = isset($data['line2']) ? $data['line2'] : null;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->country !== null) $result['country'] = $this->country;
        if ($this->city !== null) $result['city'] = $this->city;
        if ($this->postcode !== null) $result['postcode'] = $this->postcode;
        if ($this->state !== null) $result['state'] = $this->state;
        if ($this->line1 !== null) $result['line1'] = $this->line1;
        if ($this->line2 !== null) $result['line2'] = $this->line2;
        
        return $result;
    }
}
