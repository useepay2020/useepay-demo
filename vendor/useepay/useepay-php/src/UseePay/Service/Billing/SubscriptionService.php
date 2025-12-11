<?php

namespace UseePay\Service\Billing;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;

class SubscriptionService extends ApiService
{
    public function create($params)
    {
        $path = '/api/v1/subscriptions/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/subscriptions/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
    
    public function update($id, $params)
    {
        $path = sprintf('/api/v1/subscriptions/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function cancel($id)
    {
        $path = sprintf('/api/v1/subscriptions/%s/cancel', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->post($request);
    }
    
    public function listSubscriptions($params = null)
    {
        $path = '/api/v1/subscriptions';
        $request = new ApiRequest($path, $params);
        return $this->get($request);
    }
}
