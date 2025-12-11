<?php

namespace UseePay\Service\Webhook;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;
use UseePay\Util\RsaSignatureUtil;

class WebhookService extends ApiService
{
    public function verifySignature($payload, $signature, $publicKey)
    {
        return RsaSignatureUtil::verify($payload, $signature, $publicKey);
    }
    
    public function parseEvent($payload)
    {
        return json_decode($payload, true);
    }
    
    public function create($params)
    {
        $path = '/api/v1/webhooks/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/webhooks/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
}
