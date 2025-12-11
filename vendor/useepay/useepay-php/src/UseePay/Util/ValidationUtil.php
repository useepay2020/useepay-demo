<?php

namespace UseePay\Util;

use UseePay\Exception\ValidationException;

/**
 * Validation utilities
 * Compatible with PHP 5.3+
 */
class ValidationUtil
{
    /**
     * Reject if value is empty
     * 
     * @param mixed $value
     * @param string $message
     * @throws ValidationException
     * @return void
     */
    public static function rejectIfEmpty($value, $message)
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            throw new ValidationException($message);
        }
    }
    
    /**
     * Reject if value is null
     * 
     * @param mixed $value
     * @param string $message
     * @throws ValidationException
     * @return void
     */
    public static function rejectIfNull($value, $message)
    {
        if ($value === null) {
            throw new ValidationException($message);
        }
    }
    
    /**
     * Validate email format
     * 
     * @param string $email
     * @param string $message
     * @throws ValidationException
     * @return void
     */
    public static function validateEmail($email, $message = 'Invalid email format')
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException($message);
        }
    }
    
    /**
     * Validate that value is in allowed list
     * 
     * @param mixed $value
     * @param array $allowedValues
     * @param string $message
     * @throws ValidationException
     * @return void
     */
    public static function validateInList($value, $allowedValues, $message)
    {
        if (!in_array($value, $allowedValues, true)) {
            throw new ValidationException($message);
        }
    }
}
