<?php
/**
 * Test API Connection to UseePay servers
 */

echo "=== Testing UseePay API Connection ===\n\n";

$urls = [
    'Sandbox' => 'https://openapi1.uat.useepay.com',
    'Production' => 'https://openapi.useepay.com'
];

foreach ($urls as $name => $url) {
    echo "Testing $name: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);  // HEAD request
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    // Capture verbose output
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $start = microtime(true);
    $response = curl_exec($ch);
    $duration = microtime(true) - $start;
    
    if ($response === false) {
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $info = curl_getinfo($ch);
        
        echo "  ✗ FAILED!\n";
        echo "  Error Code: $errno\n";
        echo "  Error: $error\n";
        echo "  Duration: " . round($duration, 3) . " seconds\n";
        
        // Show verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        if ($verboseLog) {
            echo "\n  Verbose Output:\n";
            echo "  " . str_replace("\n", "\n  ", trim($verboseLog)) . "\n";
        }
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $connectTime = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
        
        echo "  ✓ SUCCESS!\n";
        echo "  HTTP Code: $httpCode\n";
        echo "  Connect Time: " . round($connectTime, 3) . " seconds\n";
        echo "  Total Time: " . round($duration, 3) . " seconds\n";
    }
    
    fclose($verbose);
    curl_close($ch);
    echo "\n";
}

echo "=== Test Complete ===\n";
