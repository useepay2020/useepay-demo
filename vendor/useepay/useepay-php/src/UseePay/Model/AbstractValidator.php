<?php

namespace UseePay\Model;

use UseePay\Exception\ValidationException;

/**
 * Abstract validator for request parameters
 * Compatible with PHP 5.3+
 */
abstract class AbstractValidator
{
    /**
     * Validate the object
     * 
     * @throws ValidationException
     * @return void
     */
    abstract public function validate();
}
