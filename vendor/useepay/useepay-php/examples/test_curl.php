<?php
/**
 * CURL Configuration Test Script
 * This script tests CURL settings and diagnoses timeout issues
 */

echo "=== CURL Configuration Test ===\n\n";

// 1. Check if CURL is available
if (!function_exists('curl_init')) {
    die("ERROR: CURL extension is not installed!\n");
}
echo "✓ CURL extension is available\n";

// 2. Check CURL version
$version = curl_version();
echo "✓ CURL version: " . $version['version'] . "\n";
echo "  SSL version: " . $version['ssl_version'] . "\n\n";

// 3. Test timeout settings
echo "=== Testing Timeout Settings ===\n";
$ch = curl_init();

// Set timeouts explicitly
$connectTimeout = 30;
$readTimeout = 60;

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
curl_setopt($ch, CURLOPT_TIMEOUT, $readTimeout);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

echo "Set CURLOPT_CONNECTTIMEOUT: $connectTimeout seconds\n";
echo "Set CURLOPT_TIMEOUT: $readTimeout seconds\n";
echo "Set CURLOPT_NOSIGNAL: 1\n\n";

// 4. Test a simple request
echo "=== Testing Simple HTTP Request ===\n";
curl_setopt($ch, CURLOPT_URL, 'https://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    $info = curl_getinfo($ch);
    
    echo "✗ CURL Request Failed!\n";
    echo "  Error Code: $errno\n";
    echo "  Error Message: $error\n";
    echo "\nCURL Info:\n";
    print_r($info);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    $connectTime = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
    
    echo "✓ CURL Request Successful!\n";
    echo "  HTTP Code: $httpCode\n";
    echo "  Connect Time: $connectTime seconds\n";
    echo "  Total Time: $totalTime seconds\n";
}

curl_close($ch);

// 5. Test with UseePay SDK
echo "\n=== Testing UseePay SDK Configuration ===\n";

require_once __DIR__ . '/../vendor/autoload.php';

use UseePay\UseePay;

// Set timeouts
UseePay::setConnectTimeout(30);
UseePay::setReadTimeout(60);

echo "UseePay Connect Timeout: " . UseePay::getConnectTimeout() . " seconds\n";
echo "UseePay Read Timeout: " . UseePay::getReadTimeout() . " seconds\n";
echo "UseePay Verify SSL: " . (UseePay::getVerifySslCerts() ? 'true' : 'false') . "\n";

// 6. Check PHP configuration
echo "\n=== PHP Configuration ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds\n";
echo "default_socket_timeout: " . ini_get('default_socket_timeout') . " seconds\n";
echo "curl.cainfo: " . (ini_get('curl.cainfo') ?: 'not set') . "\n";
echo "openssl.cafile: " . (ini_get('openssl.cafile') ?: 'not set') . "\n";

echo "\n=== Test Complete ===\n";
