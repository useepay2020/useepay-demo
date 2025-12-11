<?php

namespace UseePay\Net;

/**
 * API Environment Configuration
 * Compatible with PHP 5.3+
 */
class ApiEnvironment
{
    const PRODUCTION = 'production';
    const SANDBOX = 'sandbox';
    
    /**
     * @var string Environment type
     */
    private $type;
    
    /**
     * @var string Base URL
     */
    private $baseUrl;
    
    /**
     * Constructor
     * 
     * @param string $type Environment type (production or sandbox)
     */
    public function __construct($type = self::PRODUCTION)
    {
        $this->type = $type;
        $this->baseUrl = $this->getBaseUrlByType($type);
    }
    
    /**
     * Get base URL by environment type
     * 
     * @param string $type
     * @return string
     */
    private function getBaseUrlByType($type)
    {
        switch ($type) {
            case self::SANDBOX:
                return 'https://openapi1.uat.useepay.com';
            case self::PRODUCTION:
            default:
                return 'https://openapi.useepay.com';
        }
    }
    
    /**
     * Get base URL
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    /**
     * Get environment type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Check if production environment
     * 
     * @return bool
     */
    public function isProduction()
    {
        return $this->type === self::PRODUCTION;
    }
    
    /**
     * Check if sandbox environment
     * 
     * @return bool
     */
    public function isSandbox()
    {
        return $this->type === self::SANDBOX;
    }
}
