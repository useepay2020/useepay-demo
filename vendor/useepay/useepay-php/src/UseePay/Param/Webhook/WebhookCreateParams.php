<?php

namespace UseePay\Param\Webhook;

use UseePay\Model\AbstractValidator;
use UseePay\Util\ValidationUtil;
use UseePay\Exception\ValidationException;

/**
 * Webhook creation parameters
 * Compatible with PHP 5.3+
 */
class WebhookCreateParams extends AbstractValidator
{
    /**
     * @var string Webhook URL (Required)
     */
    public $url;
    
    /**
     * @var string|null API version
     */
    public $apiVersion;
    
    /**
     * @var string|null Description
     */
    public $description;
    
    /**
     * @var array Event types (Required)
     */
    public $events;
    
    /**
     * @var array|null Metadata
     */
    public $metadata;
    
    /**
     * @var string|null Status (enabled/disabled)
     */
    public $status;
    
    /**
     * Validate parameters
     * 
     * @return void
     */
    public function validate()
    {
        ValidationUtil::rejectIfEmpty($this->url, 'url required');
        
        if ($this->events === null || empty($this->events)) {
            throw new ValidationException('events required');
        }
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->url !== null) $result['url'] = $this->url;
        if ($this->apiVersion !== null) $result['api_version'] = $this->apiVersion;
        if ($this->description !== null) $result['description'] = $this->description;
        if ($this->events !== null) $result['events'] = $this->events;
        if ($this->metadata !== null) $result['metadata'] = $this->metadata;
        if ($this->status !== null) $result['status'] = $this->status;
        
        return $result;
    }
}
