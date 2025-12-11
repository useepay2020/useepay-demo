<?php

namespace UseePay\Exception;

use Exception;

/**
 * Base exception class for all UseePay exceptions
 * Compatible with PHP 5.3+
 */
abstract class UseePayException extends Exception
{
    /**
     * @var int|null HTTP status code
     */
    protected $status;
    
    /**
     * @var string|null Error code
     */
    protected $errorCode;
    
    /**
     * Constructor
     * 
     * @param int|null $status HTTP status code
     * @param string|null $code Error code
     * @param string|null $message Error message
     * @param Exception|null $previous Previous exception
     */
    public function __construct($status = null, $code = null, $message = null, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->status = $status;
        $this->errorCode = $code;
    }
    
    /**
     * Get HTTP status code
     * 
     * @return int|null
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Get error code
     * 
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    
    /**
     * Get formatted error message
     * 
     * @return string
     */
//    public function getMessage()
//    {
//        $additionalInfo = '';
//
//        if ($this->status !== null) {
//            $additionalInfo .= 'status: ' . $this->status . '; ';
//        }
//
//        if ($this->errorCode !== null) {
//            $additionalInfo .= 'code: ' . $this->errorCode . '; ';
//        }
//
//        $message = parent::getMessage();
//        return $additionalInfo . ($message ? $message : '');
//    }
}
