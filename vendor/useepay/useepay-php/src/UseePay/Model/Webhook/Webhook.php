<?php

namespace UseePay\Model\Webhook;

use UseePay\Model\BaseModel;

/**
 * Webhook object
 * Compatible with PHP 5.3+
 */
class Webhook extends BaseModel
{
    /**
     * @var string|null Webhook URL (Required)
     */
    public $url;
    
    /**
     * @var string|null Webhook description
     */
    public $description;
    
    /**
     * @var array|null List of event types
     */
    public $events;
    
    /**
     * @var array|null Metadata key-value pairs
     */
    public $metadata;
    
    /**
     * @var string|null Webhook status
     */
    public $status;
    
    // Status constants
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->url = isset($data['url']) ? $data['url'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->events = isset($data['events']) ? $data['events'] : null;
        $this->metadata = isset($data['metadata']) ? $data['metadata'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
    }
}
