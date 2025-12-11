<?php

namespace UseePay\Service\Customer;

use UseePay\Net\ApiService;
use UseePay\Net\ApiResource;
use UseePay\Model\Authentication\Authentication;
use UseePay\Model\ApiRequest;
use UseePay\Model\PageResult;
use UseePay\Model\Customer\Customer;
use UseePay\Param\Customer\CustomerCreateParams;
use UseePay\Param\Customer\CustomerUpdateParams;
use UseePay\Param\Customer\CustomerQueryParams;
use UseePay\Exception\UseePayException;

/**
 * Customer Service
 * Compatible with PHP 5.3+
 */
class CustomerService extends ApiService
{
    /**
     * Creates a new customer object
     * 
     * @param CustomerCreateParams $params
     * @return Customer
     * @throws UseePayException
     */
    public function create($params)
    {
        $path = '/api/v1/customers/create';
        $request = new ApiRequest($path, $params);
        return $this->post($request, 'UseePay\\Model\\Customer\\Customer');
    }
    
    /**
     * Retrieves a Customer object
     * 
     * @param string $id Customer ID
     * @return Customer
     * @throws UseePayException
     */
    public function retrieve($id)
    {
        $path = sprintf('/api/v1/customers/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, null);
        return $this->get($request, 'UseePay\\Model\\Customer\\Customer');
    }
    
    /**
     * Updates the specified customer
     * 
     * @param string $id Customer ID
     * @param CustomerUpdateParams $params
     * @return Customer
     * @throws UseePayException
     */
    public function update($id, $params)
    {
        $path = sprintf('/api/v1/customers/%s', ApiResource::urlEncodeId($id));
        $request = new ApiRequest($path, $params);
        return $this->post($request, 'UseePay\\Model\\Customer\\Customer');
    }
    
    /**
     * Returns a list of customers
     * 
     * @param CustomerQueryParams|null $params
     * @return PageResult
     * @throws UseePayException
     */
    public function listCustomers($params = null)
    {
        $path = '/api/v1/customers';
        $params = $params ? $params : new CustomerQueryParams();
        $request = new ApiRequest($path, $params);
        
        $response = $this->get($request);
        return new PageResult($response);
    }
}
