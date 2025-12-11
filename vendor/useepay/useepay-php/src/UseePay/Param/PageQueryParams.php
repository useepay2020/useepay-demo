<?php

namespace UseePay\Param;

/**
 * Base class for paginated query parameters
 * Compatible with PHP 5.3+
 */
class PageQueryParams
{
    /**
     * @var int Page number (1-indexed)
     */
    public $page = 1;
    
    /**
     * @var int Number of items per page
     */
    public $pageSize = 10;
    
    /**
     * @var string|null Starting cursor for pagination
     */
    public $startingAfter;
    
    /**
     * @var string|null Ending cursor for pagination
     */
    public $endingBefore;
    
    /**
     * @var int|null Limit number of results
     */
    public $limit;
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        $result = array();
        
        if ($this->page !== null) {
            $result['page'] = $this->page;
        }
        if ($this->pageSize !== null) {
            $result['page_size'] = $this->pageSize;
        }
        if ($this->startingAfter !== null) {
            $result['starting_after'] = $this->startingAfter;
        }
        if ($this->endingBefore !== null) {
            $result['ending_before'] = $this->endingBefore;
        }
        if ($this->limit !== null) {
            $result['limit'] = $this->limit;
        }
        
        return $result;
    }
}
