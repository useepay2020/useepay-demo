<?php

namespace UseePay\Model\Customer;

use UseePay\Model\BaseModel;

/**
 * Customer object
 * Compatible with PHP 5.3+
 */
class Customer extends BaseModel
{
    /**
     * @var array|null Address of the customer
     */
    public $address;
    
    /**
     * @var string|null An arbitrary string attached to the object
     */
    public $description;
    
    /**
     * @var string|null The customer's full name or business name
     */
    public $name;
    
    /**
     * @var string|null Email address of the customer
     */
    public $email;
    
    /**
     * @var string|null First name of the customer
     */
    public $firstName;
    
    /**
     * @var string|null Last name of the customer
     */
    public $lastName;
    
    /**
     * @var string|null Unique identifier in merchant's system
     */
    public $merchantCustomerId;
    
    /**
     * @var string|null Phone number of the customer
     */
    public $phone;
    
    /**
     * @var array|null A set of key-value pairs
     */
    public $metadata;
    
    /**
     * @var array|null Mailing and shipping address
     */
    public $shipping;
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->address = isset($data['address']) ? $data['address'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->email = isset($data['email']) ? $data['email'] : null;
        $this->firstName = isset($data['first_name']) ? $data['first_name'] : null;
        $this->lastName = isset($data['last_name']) ? $data['last_name'] : null;
        $this->merchantCustomerId = isset($data['merchant_customer_id']) ? $data['merchant_customer_id'] : null;
        $this->phone = isset($data['phone']) ? $data['phone'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->shipping = isset($data['shipping']) ? $data['shipping'] : null;
    }
}
