<?php

namespace UseePay\Model\Billing;

use UseePay\Model\AbstractValidator;
use UseePay\Util\ValidationUtil;
use UseePay\Exception\ValidationException;

/**
 * Recurring billing configuration
 * Compatible with PHP 5.3+
 */
class Recurring extends AbstractValidator
{
    /**
     * Billing frequency: day, week, month, year (Required)
     * @var string
     */
    public $interval;
    
    /**
     * Number of intervals between billings (Required)
     * @var int
     */
    public $intervalCount;
    
    /**
     * Amount for each period
     * @var float|string
     */
    public $unitAmount;
    
    /**
     * Total number of billing cycles
     * @var int|null
     */
    public $totalBillingCycles;
    
    /**
     * Current billing cycles
     * @var int|null
     */
    public $currentBillingCycles;
    
    // Interval constants
    const INTERVAL_DAY = 'day';
    const INTERVAL_WEEK = 'week';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR = 'year';
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->interval = isset($data['interval']) ? $data['interval'] : null;
        $this->intervalCount = isset($data['interval_count']) ? $data['interval_count'] : null;
        $this->unitAmount = isset($data['unit_amount']) ? $data['unit_amount'] : null;
        $this->totalBillingCycles = isset($data['total_billing_cycles']) ? $data['total_billing_cycles'] : null;
        $this->currentBillingCycles = isset($data['current_billing_cycles']) ? $data['current_billing_cycles'] : null;
    }
    
    /**
     * Validate recurring configuration
     * 
     * @return void
     * @throws ValidationException
     */
    public function validate()
    {
        ValidationUtil::rejectIfEmpty($this->interval, 'recurring.interval required');
        ValidationUtil::rejectIfEmpty($this->intervalCount, 'recurring.intervalCount required');
        ValidationUtil::rejectIfEmpty($this->unitAmount, 'recurring.unitAmount required');
        
        if ($this->intervalCount < 1) {
            throw new ValidationException('recurring.intervalCount cannot be less than 1');
        }
        
        if ($this->unitAmount < 0) {
            throw new ValidationException('recurring.unitAmount cannot be less than 0');
        }
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->interval !== null) $result['interval'] = $this->interval;
        if ($this->intervalCount !== null) $result['interval_count'] = $this->intervalCount;
        if ($this->unitAmount !== null) $result['unit_amount'] = $this->unitAmount;
        if ($this->totalBillingCycles !== null) $result['total_billing_cycles'] = $this->totalBillingCycles;
        if ($this->currentBillingCycles !== null) $result['current_billing_cycles'] = $this->currentBillingCycles;
        
        return $result;
    }
}
