<?php

namespace UseePay\Net;

/**
 * API Resource utilities
 * Compatible with PHP 5.3+
 */
class ApiResource
{
    /**
     * URL encode an ID for use in API paths
     * 
     * @param string $id
     * @return string
     */
    public static function urlEncodeId($id)
    {
        return rawurlencode($id);
    }
    
    /**
     * Build query string from parameters
     * 
     * @param array $params
     * @return string
     */
    public static function buildQueryString($params)
    {
        // Remove null and empty values
        $params = array_filter($params, array(__CLASS__, 'filterNonEmpty'));
        
        if (empty($params)) {
            return '';
        }
        
        return http_build_query($params);
    }
    
    /**
     * Filter callback for non-empty values
     * 
     * @param mixed $value
     * @return bool
     */
    private static function filterNonEmpty($value)
    {
        return $value !== null && $value !== '';
    }
}
