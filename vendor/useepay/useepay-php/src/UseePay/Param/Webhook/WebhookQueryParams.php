<?php

namespace UseePay\Param\Webhook;

use UseePay\Param\PageQueryParams;

/**
 * Webhook query parameters
 * Compatible with PHP 5.3+
 */
class WebhookQueryParams extends PageQueryParams
{
    /**
     * @var string|null Status filter
     */
    public $status;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = parent::toArray();
        
        if ($this->status !== null) {
            $result['status'] = $this->status;
        }
        
        return $result;
    }
}
