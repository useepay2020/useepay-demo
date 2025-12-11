<?php

namespace UseePay\Service\Payment;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\ApiRequest;
use UseePay\Exception\UseePayException;

/**
 * Payment Intent Service
 * Compatible with PHP 5.3+
 */
class PaymentIntentService extends ApiService
{
    /**
     * Creates a new payment intent
     * 
     * @param array $params
     * @return mixed
     * @throws UseePayException
     */
    public function create($params)
    {
        $path = '/api/v1/payment_intents/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    /**
     * Updates properties on a PaymentIntent object without confirming.
     * @param id
     * @param params
     * @return
     */
    public function update($id, $params = array())
    {
        $path = sprintf('/api/v1/payment_intents/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }

    /**
     * Retrieves a payment intent
     * 
     * @param string $id Payment intent ID
     * @return mixed
     * @throws UseePayException
     */
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/payment_intents/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request);
    }
    
    /**
     * Confirms a payment intent
     * 
     * @param string $id Payment intent ID
     * @param array $params
     * @return mixed
     * @throws UseePayException
     */
    public function confirm($id, $params = array())
    {
        $path = sprintf('/api/v1/payment_intents/%s/confirm', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }

    /**
     * A PaymentIntent can be cancelled when it is in one of these statuses: REQUIRES_PAYMENT_METHOD, REQUIRES_CUSTOMER_ACTION, REQUIRES_CAPTURE.
     * Any outstanding, un-captured funds will be refunded once cancelled.
     * @param id
     * @param params
     * @return
     */
    public function cancel($id,$params = array())
    {
        $path = sprintf('/api/v1/payment_intents/%s/cancel', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
    /**
     * Capture the funds of an existing uncaptured PaymentIntent when its status is requires_capture.
     *
     * Uncaptured PaymentIntents are cancelled a set number of days (7 by default) after their creation.
     * @param id
     * @param params
     * @return
     */
    public function capture($id, $params = array())
    {
        $path = sprintf('/api/v1/payment_intents/%s/capture', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request);
    }
}
