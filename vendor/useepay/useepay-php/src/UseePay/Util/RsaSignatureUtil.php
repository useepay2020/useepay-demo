<?php

namespace UseePay\Util;

use RuntimeException;

/**
 * RSA Signature utilities
 * Compatible with PHP 5.3+
 */
class RsaSignatureUtil
{
    /**
     * Sign data with private key
     * 
     * @param string $data
     * @param string $privateKey
     * @return string Base64 encoded signature
     * @throws RuntimeException
     */
    public static function sign($data, $privateKey)
    {
        $key = openssl_pkey_get_private($privateKey);
        if ($key === false) {
            throw new RuntimeException('Invalid private key');
        }
        
        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
        
        return base64_encode($signature);
    }
    
    /**
     * Verify signature with public key
     * 
     * @param string $data
     * @param string $signature Base64 encoded signature
     * @param string $publicKey
     * @return bool
     * @throws RuntimeException
     */
    public static function verify($data, $signature, $publicKey)
    {
        $key = openssl_pkey_get_public($publicKey);
        if ($key === false) {
            throw new RuntimeException('Invalid public key');
        }
        
        $result = openssl_verify($data, base64_decode($signature), $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
        
        return $result === 1;
    }
    
    /**
     * Mask sensitive string (show first and last N characters)
     * 
     * @param string $input
     * @param int $length Number of characters to show at start and end
     * @return string
     */
    public static function mask($input, $length = 10)
    {
        if ($input === null || strlen($input) <= 2 * $length) {
            return $input;
        }
        
        $prefix = substr($input, 0, $length);
        $suffix = substr($input, -$length);
        
        return $prefix . '***' . $suffix;
    }
}
