<?php

namespace UseePay\Model\Authentication;

use UseePay\Model\AbstractValidator;
use UseePay\Util\ValidationUtil;
use UseePay\Util\RsaSignatureUtil;

/**
 * Authentication credentials
 * Compatible with PHP 5.3+
 */
class Authentication extends AbstractValidator
{
    /**
     * @var string Merchant number
     */
    public $merchantNo;
    
    /**
     * @var string Application ID
     */
    public $appId;
    
    /**
     * @var string API key
     */
    public $apiKey;
    
    /**
     * @var string|null API version
     */
    public $apiVersion;
    
    /**
     * Constructor
     * 
     * @param string $merchantNo
     * @param string $appId
     * @param string $apiKey
     * @param string|null $apiVersion
     */
    public function __construct($merchantNo, $appId, $apiKey, $apiVersion = null)
    {
        $this->merchantNo = $merchantNo;
        $this->appId = $appId;
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;
    }
    
    /**
     * Validate authentication credentials
     * 
     * @return void
     */
    public function validate()
    {
        ValidationUtil::rejectIfEmpty($this->merchantNo, 'merchantNo required');
        ValidationUtil::rejectIfEmpty($this->appId, 'appId required');
        ValidationUtil::rejectIfEmpty($this->apiKey, 'apiKey required');
    }
    
    /**
     * Get string representation
     * 
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "Authentication{merchantNo='%s', appId='%s', apiKey='%s', apiVersion='%s'}",
            $this->merchantNo,
            $this->appId,
            RsaSignatureUtil::mask($this->apiKey, 10),
            $this->apiVersion
        );
    }
}
