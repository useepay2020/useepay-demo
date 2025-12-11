<?php

namespace UseePay\Service\Billing;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;

class InvoiceService extends ApiService
{
    public function create($params)
    {
        $path = '/api/v1/invoices/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/invoices/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
    
    public function update($id, $params)
    {
        $path = sprintf('/api/v1/invoices/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function finalize($id)
    {
        $path = sprintf('/api/v1/invoices/%s/finalize', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->post($request);
    }
    
    public function listInvoices($params = null)
    {
        $path = '/api/v1/invoices';
        $request = new ApiRequest($path, $params);
        return $this->get($request);
    }
}
