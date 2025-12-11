<?php

namespace UseePay;

use UseePay\Net\ApiService;
use UseePay\Net\ApiEnvironment;
use UseePay\Model\Authentication\Authentication;
use UseePay\Service\Customer\CustomerService;
use UseePay\Service\Billing\SubscriptionService;
use UseePay\Service\Billing\InvoiceService;
use UseePay\Service\Payment\PaymentIntentService;
use UseePay\Service\Payment\RefundService;
use UseePay\Service\Webhook\WebhookService;

class UseePayClient extends ApiService
{
    public static function withEnvironment($environmentType, $authentication)
    {
        $client = new self($authentication);
        $client->setEnvironment(new ApiEnvironment($environmentType));
        return $client;
    }
    
    public function customers()
    {
        $service = new CustomerService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
    
    public function subscriptions()
    {
        $service = new SubscriptionService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
    
    public function invoices()
    {
        $service = new InvoiceService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
    
    public function paymentIntents()
    {
        $service = new PaymentIntentService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
    
    public function refunds()
    {
        $service = new RefundService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
    
    public function webhooks()
    {
        $service = new WebhookService($this->getAuthentication());
        $service->setEnvironment($this->getEnvironment());
        return $service;
    }
}
