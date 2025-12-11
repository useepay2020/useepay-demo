<?php

namespace UseePay\Service\Payment;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;

class RefundService extends ApiService
{
    public function create($params)
    {
        $path = '/api/v1/refunds/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/refunds/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
    
    public function listRefunds($params = null)
    {
        $path = '/api/v1/refunds';
        $request = new ApiRequest($path, $params);
        return $this->get($request);
    }
}
