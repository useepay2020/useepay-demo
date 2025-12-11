<?php

namespace UseePay\Model;

/**
 * Paginated result wrapper
 * Compatible with PHP 5.3+
 */
class PageResult
{
    /**
     * @var array List of data items
     */
    public $data;
    
    /**
     * @var bool Whether there are more items
     */
    public $hasMore;
    
    /**
     * @var int Total count
     */
    public $total;
    
    /**
     * @var int Current page
     */
    public $page;
    
    /**
     * @var int Page size
     */
    public $pageSize;
    
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->data = isset($data['data']) ? $data['data'] : array();
        $this->hasMore = isset($data['has_more']) ? $data['has_more'] : false;
        $this->total = isset($data['total']) ? $data['total'] : 0;
        $this->page = isset($data['page']) ? $data['page'] : 1;
        $this->pageSize = isset($data['page_size']) ? $data['page_size'] : 10;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'data' => $this->data,
            'has_more' => $this->hasMore,
            'total' => $this->total,
            'page' => $this->page,
            'page_size' => $this->pageSize,
        );
    }
}
