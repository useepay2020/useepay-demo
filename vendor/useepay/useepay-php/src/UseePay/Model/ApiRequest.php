<?php

namespace UseePay\Model;

/**
 * API Request wrapper
 * Compatible with PHP 5.3+
 */
class ApiRequest
{
    /**
     * @var string API path
     */
    private $path;
    
    /**
     * @var mixed Request parameters
     */
    private $params;
    
    /**
     * Constructor
     * 
     * @param string $path
     * @param mixed $params
     */
    public function __construct($path, $params = null)
    {
        $this->path = $path;
        $this->params = $params;
    }
    
    /**
     * Get path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Get parameters
     * 
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}
