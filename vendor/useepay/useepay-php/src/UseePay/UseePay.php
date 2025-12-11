<?php

namespace UseePay;

/**
 * UseePay SDK Configuration Class
 * Compatible with PHP 5.3+
 */
class UseePay
{
    const VERSION = '1.0.0';

    const DEFAULT_CONNECT_TIMEOUT = 6;
    const DEFAULT_READ_TIMEOUT = 30;

    const CONTENT_TYPE = 'Content-Type';
    const CHARSET = 'UTF-8';

    /**
     * @var int Connection timeout in seconds (-1 means unset, will use default)
     */
    private static $connectTimeout = -1;

    /**
     * @var int Read timeout in seconds (-1 means unset, will use default)
     */
    private static $readTimeout = -1;

    /**
     * @var int Maximum number of network retries
     */
    private static $maxNetworkRetries = 0;

    /**
     * @var bool Whether to verify SSL certificates
     */
    private static $verifySslCerts = true;

    /**
     * @var string|null Custom CA bundle path
     */
    private static $caBundlePath = null;

    /**
     * Get connection timeout
     *
     * @return int timeout in seconds
     */
    public static function getConnectTimeout()
    {
        if (self::$connectTimeout === -1) {
            return self::DEFAULT_CONNECT_TIMEOUT;
        }
        return self::$connectTimeout;
    }

    /**
     * Set connection timeout
     *
     * @param int $timeout timeout in seconds
     * @return void
     */
    public static function setConnectTimeout($timeout)
    {
        self::$connectTimeout = $timeout;
    }

    /**
     * Get read timeout
     *
     * @return int timeout in seconds
     */
    public static function getReadTimeout()
    {
        if (self::$readTimeout === -1) {
            return self::DEFAULT_READ_TIMEOUT;
        }
        return self::$readTimeout;
    }

    /**
     * Set read timeout
     *
     * @param int $timeout timeout in seconds
     * @return void
     */
    public static function setReadTimeout($timeout)
    {
        self::$readTimeout = $timeout;
    }

    /**
     * Get maximum number of network retries
     *
     * @return int
     */
    public static function getMaxNetworkRetries()
    {
        return self::$maxNetworkRetries;
    }

    /**
     * Set maximum number of network retries
     *
     * @param int $maxRetries
     * @return void
     */
    public static function setMaxNetworkRetries($maxRetries)
    {
        self::$maxNetworkRetries = $maxRetries;
    }

    /**
     * Get whether to verify SSL certificates
     *
     * @return bool
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * Set whether to verify SSL certificates
     *
     * @param bool $verify
     * @return void
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * Get CA bundle path
     *
     * @return string|null
     */
    public static function getCaBundlePath()
    {
        return self::$caBundlePath;
    }

    /**
     * Set custom CA bundle path
     *
     * @param string $path Path to CA bundle file
     * @return void
     */
    public static function setCaBundlePath($path)
    {
        self::$caBundlePath = $path;
    }
}