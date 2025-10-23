<?php

namespace UseePayDemo\Traits;

/**
 * Trait Loggable
 * 提供日志记录功能
 */
trait Loggable
{
    /**
     * 日志目录
     * @var string
     */
    protected $logDir;

    /**
     * 初始化日志目录
     */
    protected function initLogDir()
    {
        $this->logDir = __DIR__ . '/../../logs';
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    /**
     * 记录日志
     * 
     * @param string $message 日志信息
     * @param string $level 日志级别 (info, error, debug)
     * @param array $context 上下文数据
     * @param string $logFile 日志文件名（不包含路径）
     * @return void
     */
    protected function log($message, $level = 'info', array $context = [], $logFile = 'app')
    {
        if (!isset($this->logDir)) {
            $this->initLogDir();
        }
        
        $logFile = $this->logDir . '/' . $logFile . '_' . date('Y-m-d') . '.log';
        
        $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '';
        $logMessage = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $contextStr
        );
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // 如果是错误，同时输出到错误日志
        if ($level === 'error') {
            error_log($logMessage);
        }
    }
    
    /**
     * 记录请求信息
     * 
     * @param string $logFile 日志文件名（不包含路径）
     * @return void
     */
    protected function logRequest($logFile = 'request')
    {
        $requestData = [
            'method' => $_SERVER['REQUEST_METHOD'],
            'uri' => $_SERVER['REQUEST_URI'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'get_params' => $_GET,
            'post_data' => $_POST,
            'raw_input' => file_get_contents('php://input')
        ];
        
        $this->log('Incoming request', 'info', $requestData, $logFile);
    }

    /**
     * 记录异常信息
     * 
     * @param \Exception $e 异常对象
     * @param string $message 自定义错误信息
     * @param string $logFile 日志文件名（不包含路径）
     * @return void
     */
    protected function logException(\Exception $e, $message = '', $logFile = 'error')
    {
        $context = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        $this->log(
            $message ?: 'Exception occurred',
            'error',
            $context,
            $logFile
        );
    }
}
