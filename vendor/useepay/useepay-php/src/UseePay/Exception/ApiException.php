<?php

namespace UseePay\Exception;

/**
 * API Exception
 * Thrown when API returns an error response
 */
class ApiException extends UseePayException
{
    /**
     * Constructor
     * 
     * @param int|null $status HTTP status code
     * @param string|null $code Error code
     * @param string|null $message Error message
     */
    public function __construct($status = null, $code = null, $message = null)
    {
        parent::__construct($status, $code, $message);
    }
}
