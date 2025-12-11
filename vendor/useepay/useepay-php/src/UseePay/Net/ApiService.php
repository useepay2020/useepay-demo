<?php

namespace UseePay\Net;

use UseePay\UseePay;
use UseePay\Model\Authentication\Authentication;
use UseePay\Model\ApiRequest;
use UseePay\Model\AbstractValidator;
use UseePay\Exception\UseePayException;
use UseePay\Exception\ApiException;
use UseePay\Util\HttpClient;

/**
 * Base API service class
 * Compatible with PHP 5.3+
 */
abstract class ApiService
{
    /**
     * @var ApiEnvironment
     */
    protected $environment;
    
    /**
     * @var Authentication
     */
    protected $authentication;
    
    /**
     * Constructor
     * 
     * @param Authentication $authentication
     */
    public function __construct($authentication)
    {
        $this->authentication = $authentication;
        $this->environment = new ApiEnvironment(ApiEnvironment::PRODUCTION);
    }
    
    /**
     * Set environment
     * 
     * @param ApiEnvironment $environment
     * @return void
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
    
    /**
     * Get environment
     * 
     * @return ApiEnvironment
     */
    public function getEnvironment()
    {
        return $this->environment ? $this->environment : new ApiEnvironment(ApiEnvironment::PRODUCTION);
    }
    
    /**
     * Get authentication
     * 
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }
    
    /**
     * Set authentication
     * 
     * @param Authentication $authentication
     * @return void
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
    }
    
    /**
     * HTTP GET method
     * 
     * @param ApiRequest $request
     * @param string|null $responseClass
     * @return mixed
     * @throws UseePayException
     */
    protected function get($request, $responseClass = null)
    {
        $url = $this->getEnvironment()->getBaseUrl() . $request->getPath();
        $params = null;
        
        if ($request->getParams() !== null) {
            if ($request->getParams() instanceof AbstractValidator) {
                $request->getParams()->validate();
            }
            
            $params = $this->convertToArray($request->getParams());
        }
        
        $response = HttpClient::get($url, $this->buildHeaders(), $params);
        return $this->parseResponse($response, $responseClass);
    }
    
    /**
     * HTTP POST method
     * 
     * @param ApiRequest $request
     * @param string|null $responseClass
     * @return mixed
     * @throws UseePayException
     */
    protected function post($request, $responseClass = null)
    {
        $url = $this->getEnvironment()->getBaseUrl() . $request->getPath();
        $body = null;
        
        if ($request->getParams() !== null) {
            if ($request->getParams() instanceof AbstractValidator) {
                $request->getParams()->validate();
            }
            
            $params = $this->convertToArray($request->getParams());
            $body = json_encode($params);
        }
        
        $response = HttpClient::post($url, $this->buildHeaders(), $body);
        return $this->parseResponse($response, $responseClass);
    }
    
    /**
     * Build HTTP headers
     * 
     * @return array
     */
    private function buildHeaders()
    {
        return array(
            'Connection' => 'keep-alive',
            UseePay::CONTENT_TYPE => 'application/json',
            'x-merchant-no' => $this->authentication->merchantNo,
            'x-app-id' => $this->authentication->appId,
            'x-api-key' => $this->authentication->apiKey,
            'x-api-version' => $this->authentication->apiVersion,
            'x-sdk-version' => UseePay::VERSION,
            'x-php-version' => PHP_VERSION,
            'x-timestamp' => (string)(time() * 1000),
        );
    }
    
    /**
     * Parse JSON response
     * 
     * @param string $response
     * @param string|null $responseClass
     * @return mixed
     * @throws ApiException
     */
    private function parseResponse($response, $responseClass = null)
    {
        $data = json_decode($response, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(null, 'json_error', 'Failed to parse JSON response: ' . json_last_error_msg());
        }
        
        if ($responseClass === null) {
            return $data;
        }
        
        if (class_exists($responseClass)) {
            return new $responseClass($data);
        }
        
        return $data;
    }
    
    /**
     * Convert object to array
     * 
     * @param mixed $obj
     * @return array
     */
    private function convertToArray($obj)
    {
        if (is_array($obj)) {
            return $obj;
        }
        
        if (method_exists($obj, 'toArray')) {
            return $obj->toArray();
        }
        
        return json_decode(json_encode($obj), true);
    }
}
