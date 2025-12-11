<?php

namespace UseePay\Model\Billing;

/**
 * Cancellation details for subscription
 * Compatible with PHP 5.3+
 */
class CancellationDetails
{
    /**
     * @var string|null Additional comments about cancellation
     */
    public $comment;
    
    /**
     * @var string|null Feedback reason
     */
    public $feedback;
    
    // Feedback constants
    const FEEDBACK_CUSTOMER_SERVICE = 'customer_service';
    const FEEDBACK_LOW_QUALITY = 'low_quality';
    const FEEDBACK_MISSING_FEATURES = 'missing_features';
    const FEEDBACK_OTHER = 'other';
    const FEEDBACK_SWITCHED_SERVICE = 'switched_service';
    const FEEDBACK_TOO_COMPLEX = 'too_complex';
    const FEEDBACK_TOO_EXPENSIVE = 'too_expensive';
    const FEEDBACK_UNUSED = 'unused';
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->comment = isset($data['comment']) ? $data['comment'] : null;
        $this->feedback = isset($data['feedback']) ? $data['feedback'] : null;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->comment !== null) $result['comment'] = $this->comment;
        if ($this->feedback !== null) $result['feedback'] = $this->feedback;
        
        return $result;
    }
}
