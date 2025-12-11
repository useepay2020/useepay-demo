<?php

namespace UseePay\Model;

/**
 * Base model for all UseePay objects
 * Compatible with PHP 5.3+
 */
abstract class BaseModel
{
    /**
     * @var string|null Unique identifier for the object
     */
    public $id;
    
    /**
     * @var string|null Merchant no of UseePay
     */
    public $merchantNo;
    
    /**
     * @var string|null Application id of merchant
     */
    public $appId;
    
    /**
     * @var string|null Time at which the object was created
     */
    public $createAt;
    
    /**
     * @var string|null Time at which the object was last modified
     */
    public $modifyAt;
    
    /**
     * @var string|null The version of API
     */
    public $apiVersion;
    
    /**
     * Constructor - Initialize from array
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->merchantNo = isset($data['merchant_no']) ? $data['merchant_no'] : null;
        $this->appId = isset($data['app_id']) ? $data['app_id'] : null;
        $this->createAt = isset($data['create_at']) ? $data['create_at'] : null;
        $this->modifyAt = isset($data['modify_at']) ? $data['modify_at'] : null;
        $this->apiVersion = isset($data['api_version']) ? $data['api_version'] : null;
    }
    
    /**
     * Convert model to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        foreach (get_object_vars($this) as $key => $value) {
            if ($value !== null) {
                $snakeKey = $this->camelToSnake($key);
                $result[$snakeKey] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Convert model to JSON
     * 
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Convert camelCase to snake_case
     * 
     * @param string $input
     * @return string
     */
    protected function camelToSnake($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
