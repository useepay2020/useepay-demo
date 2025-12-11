<?php

namespace UseePay\Util;

use UseePay\UseePay;
use UseePay\Exception\ApiException;
use UseePay\Exception\AuthenticationException;
use UseePay\Net\RequestMethod;

/**
 * HTTP Client for making API requests
 * Compatible with PHP 5.3+
 */
class HttpClient
{
    const USER_AGENT = 'UseePay-PHP/1.0.0';

    /**
     * @var string|null Cached CA bundle path
     */
    private static $cachedCaBundlePath = null;

    /**
     * Send GET request
     *
     * @param string $url
     * @param array $headers
     * @param array|null $params
     * @return string Response body
     * @throws ApiException
     * @throws AuthenticationException
     */
    public static function get($url, $headers, $params = null)
    {
        if ($params) {
            $queryString = http_build_query(array_filter($params, array(__CLASS__, 'filterEmptyValues')));

            if ($queryString) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
            }
        }

        return self::request(RequestMethod::GET, $url, $headers, null);
    }

    /**
     * Send POST request
     *
     * @param string $url
     * @param array $headers
     * @param string|null $body
     * @return string Response body
     * @throws ApiException
     * @throws AuthenticationException
     */
    public static function post($url, $headers, $body = null)
    {
        return self::request(RequestMethod::POST, $url, $headers, $body);
    }

    /**
     * Filter callback for empty values
     *
     * @param mixed $value
     * @return bool
     */
    private static function filterEmptyValues($value)
    {
        return $value !== null && $value !== '';
    }

    /**
     * Automatically detect CA bundle path
     * Similar to Java's automatic certificate trust store detection
     *
     * @return string|null
     */
    private static function detectCaBundlePath()
    {
        if (self::$cachedCaBundlePath !== null) {
            return self::$cachedCaBundlePath;
        }

        // Check if user set a custom CA bundle path
        $customPath = UseePay::getCaBundlePath();
        if ($customPath !== null && file_exists($customPath)) {
            self::$cachedCaBundlePath = $customPath;
            return $customPath;
        }

        // Check project root directory for bundled cacert.pem (highest priority)
        $projectCacert = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'cacert.pem';
        if (file_exists($projectCacert)) {
            self::$cachedCaBundlePath = $projectCacert;
            return $projectCacert;
        }

        // Common CA bundle locations on different systems
        $possiblePaths = array(
            // Windows with PHP
            'C:\php\extras\ssl\cacert.pem',
            'C:\xampp\php\extras\ssl\cacert.pem',
            'C:\wamp\bin\php\php' . PHP_VERSION . '\extras\ssl\cacert.pem',
            // Linux/Unix
            '/etc/ssl/certs/ca-certificates.crt',                // Debian/Ubuntu/Gentoo
            '/etc/pki/tls/certs/ca-bundle.crt',                  // Fedora/RHEL/CentOS
            '/etc/ssl/ca-bundle.pem',                            // OpenSUSE
            '/etc/ssl/cert.pem',                                 // OpenBSD
            '/usr/local/share/certs/ca-root-nss.crt',           // FreeBSD
            '/etc/pki/tls/cacert.pem',                          // Amazon Linux
            '/etc/pki/ca-trust/extracted/pem/tls-ca-bundle.pem', // CentOS/RHEL 7+
            // macOS
            '/usr/local/etc/openssl/cert.pem',                  // Homebrew
            '/opt/local/share/curl/curl-ca-bundle.crt',         // MacPorts
        );

        // Check php.ini settings
        $iniCaFile = ini_get('curl.cainfo');
        if ($iniCaFile && file_exists($iniCaFile)) {
            self::$cachedCaBundlePath = $iniCaFile;
            return $iniCaFile;
        }

        $iniCaPath = ini_get('openssl.cafile');
        if ($iniCaPath && file_exists($iniCaPath)) {
            self::$cachedCaBundlePath = $iniCaPath;
            return $iniCaPath;
        }

        // Check common paths
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                self::$cachedCaBundlePath = $path;
                return $path;
            }
        }

        // No CA bundle found
        self::$cachedCaBundlePath = false;
        return null;
    }

    /**
     * Make HTTP request
     *
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string|null $body
     * @return string Response body
     * @throws ApiException
     * @throws AuthenticationException
     */
    private static function request($method, $url, $headers, $body = null)
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new ApiException(null, 'curl_init_failed', 'Failed to initialize cURL');
        }

        // Set URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Set timeouts (ensure they are positive integers)
        $connectTimeout = max(1, (int)UseePay::getConnectTimeout());
        $readTimeout = max(1, (int)UseePay::getReadTimeout());
        
        // Use explicit timeout in seconds (not milliseconds)
        // CURLOPT_CONNECTTIMEOUT_MS and CURLOPT_TIMEOUT_MS should NOT be set
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $readTimeout);
        
        // Explicitly disable millisecond timeouts to prevent conflicts
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        // Return response as string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Include header in output
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Configure SSL verification (similar to Java's automatic trust store)
        if (UseePay::getVerifySslCerts()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            // Try to automatically detect CA bundle
            $caBundlePath = self::detectCaBundlePath();
            if ($caBundlePath) {
                curl_setopt($ch, CURLOPT_CAINFO, $caBundlePath);
            }
            // If no CA bundle found, cURL will use its default (may fail)
        } else {
            // Disable SSL verification (not recommended for production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // Set headers
        $headerArray = array(self::USER_AGENT);
        foreach ($headers as $key => $value) {
            $headerArray[] = $key . ': ' . $value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);

        // Set body for POST requests
        if ($body !== null && $method === RequestMethod::POST) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        // Execute request
        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            // Provide helpful error message for SSL issues
            if (strpos($error, 'SSL certificate') !== false) {
                $helpMessage = $error . "\n\n" .
                    "To fix this issue, you can:\n" .
                    "1. Download CA bundle from https://curl.se/ca/cacert.pem\n" .
                    "2. Save it to a location (e.g., C:\\php\\extras\\ssl\\cacert.pem)\n" .
                    "3. Set it in your code: UseePay::setCaBundlePath('C:\\\\php\\\\extras\\\\ssl\\\\cacert.pem');\n" .
                    "4. Or configure php.ini: curl.cainfo=\"C:\\php\\extras\\ssl\\cacert.pem\"";
                throw new ApiException(null, 'ssl_error', $helpMessage);
            }

            // Provide helpful error message for timeout issues
            if ($errno === 28 || strpos($error, 'timeout') !== false || strpos($error, 'timed out') !== false) {
                $helpMessage = "CURL timeout error: $error\n\n" .
                    "Timeout settings: Connect=$connectTimeout seconds, Read=$readTimeout seconds\n" .
                    "This may be caused by:\n" .
                    "1. Network connectivity issues\n" .
                    "2. Firewall blocking the connection\n" .
                    "3. Server not responding\n" .
                    "4. DNS resolution problems\n\n" .
                    "Try increasing timeout: UseePay::setConnectTimeout(30); UseePay::setReadTimeout(60);";
                throw new ApiException(null, 'timeout_error', $helpMessage);
            }

            throw new ApiException(null, 'curl_error', 'CURL error: ' . $error);
        }

        // Get response info
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        // Extract body
        $responseBody = substr($response, $headerSize);

        // Handle response
        return self::handleResponse($httpCode, $responseBody);
    }

    /**
     * Handle HTTP response
     *
     * @param int $httpCode
     * @param string $responseBody
     * @return string
     * @throws ApiException
     * @throws AuthenticationException
     */
    private static function handleResponse($httpCode, $responseBody)
    {
        // Success (2xx)
        if ($httpCode >= 200 && $httpCode < 300) {
            return $responseBody;
        }

        // Parse error response
        $errorData = json_decode($responseBody, true);
        $code = 'error';
        $message = $responseBody;

        if (is_array($errorData)) {
            $code = isset($errorData['code']) ? $errorData['code'] : 'error';
            $message = isset($errorData['message']) ? $errorData['message'] : $responseBody;
        }

        // Authentication error (401)
        if ($httpCode === 401) {
            throw new AuthenticationException($httpCode, $code, $message);
        }

        // Other API errors
        throw new ApiException($httpCode, $code, $message);
    }
}