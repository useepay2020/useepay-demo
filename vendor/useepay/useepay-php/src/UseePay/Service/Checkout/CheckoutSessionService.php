<?php

namespace UseePay\Service\Checkout;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;
use UseePay\Exception\UseePayException;

/**
 * Checkout Session Service
 * Compatible with PHP 5.3+
 */
class CheckoutSessionService extends ApiService
{
    /**
     * Creates a new checkout session
     * 
     * @param array $params
     * @return mixed
     * @throws UseePayException
     */
    public function create($params)
    {
        $path = '/api/v1/checkout_sessions';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    /**
     * Retrieves a checkout session by ID
     * 
     * @param string $sessionId Checkout session ID
     * @return mixed
     * @throws UseePayException
     */
    public function retrieve($sessionId)
    {
        $path = sprintf('/api/v1/checkout_sessions/%s', ApiResource::urlEncodeId($sessionId));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
}
