<?php

namespace UseePay\Exception;

/**
 * Validation Exception
 * Thrown when request parameters fail validation
 */
class ValidationException extends UseePayException
{
    /**
     * Constructor
     * 
     * @param string $message Error message
     */
    public function __construct($message)
    {
        parent::__construct(null, null, $message);
    }
}
