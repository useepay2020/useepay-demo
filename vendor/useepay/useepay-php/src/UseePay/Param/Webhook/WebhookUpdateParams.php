<?php

namespace UseePay\Param\Webhook;

/**
 * Webhook update parameters
 * Compatible with PHP 5.3+
 */
class WebhookUpdateParams
{
    /**
     * @var string|null Webhook URL
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
     * @var array|null Event types
     */
    public $events;
    
    /**
     * @var array|null Metadata
     */
    public $metadata;
    
    /**
     * @var string|null Status
     */
    public $status;
    
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
