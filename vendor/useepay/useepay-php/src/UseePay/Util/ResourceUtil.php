<?php

namespace UseePay\Util;

/**
 * Resource utility for reading files
 * Compatible with PHP 5.3+
 */
class ResourceUtil
{
    /**
     * Load JSON file and convert to object
     * 
     * @param string $fileName File name
     * @param string $responseType Class name to instantiate
     * @return mixed
     * @throws RuntimeException
     */
    public static function fromJson($fileName, $responseType)
    {
        try {
            $json = self::load($fileName);
            $data = json_decode($json, true);
            
            if (class_exists($responseType)) {
                return new $responseType($data);
            }
            
            return $data;
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
    
    /**
     * Load file content as string
     * 
     * @param string $fileName File name
     * @return string
     * @throws RuntimeException
     */
    public static function load($fileName)
    {
        $filePath = dirname(__FILE__) . '/../../../resources/' . $fileName;
        
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found: ' . $fileName);
        }
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException('Failed to read file: ' . $fileName);
        }
        
        return $content;
    }
}
