<?php

class LoggerManager {
    private mixed $config;
    private array $loggers = [];
    private Logger $logger;

    public function __construct($config) {
        $this->config = $config;

        $this->logger = new Logger($this->config, "LoggerManager");
    }

    public function getLogger($name) {
        if (isset($this->loggers[$name])) {
            return $this->loggers[$name];
        }
        $newLogger = new Logger($this->config, $name);
        $this->loggers[$name] = $newLogger;
        return $newLogger;
    }
}

class Logger {
    private $config;
    private $name;

    public function __construct($config, $name = 'default') {
        $this->config = $config;
        $this->name = $name;
    }

    public function message(string $message, string $type, string $file) {
        $message = '[' . date('Y-m-d H:i:s') . '] [' . $_SERVER['REMOTE_ADDR'] . '] [' . $this->name . '] [' . $type . '] ' . $message . PHP_EOL;
        $filePath = $this->config->get('LOGGER.LOG_PATH') . '/' . $file . '.log';

        if (!is_file($filePath)) {
            touch($filePath);
        }

        $index = 0;
        while (filesize($filePath) > $this->config->get('LOGGER.MAX_LOG_SIZE')) {
            $index++;
            $filePath = $this->config->get('LOGGER.LOG_PATH') . '/' . $file . '_' . $index .'.log';
            if (!is_file($filePath)) {
                touch($filePath);
            }
        }

        file_put_contents($filePath, $message, FILE_APPEND);
    }

    public function error(string $message) {
        if ($this->config->get('LOGGER.SHOW_ERRORS_ON_SCREEN')) {
            echo $message;
        }

        $this->message(
            $message,
            'error',
            $this->config->get('LOGGER.ERROR_LOG_FILE') ??
                $this->config->get('LOGGER.LOG_FILE')
        );
    }

    public function warning(string $message) {
        $this->message(
            $message,
            'warning',
            $this->config->get('LOGGER.WARNING_LOG_FILE') ??
                $this->config->get('LOGGER.LOG_FILE')
        );
    }

    public function info(string $message) {
        $this->message(
            $message,
            'info',
            $this->config->get('LOGGER.INFO_LOG_FILE') ??
                $this->config->get('LOGGER.LOG_FILE')
        );
    }

    public function debug(string $message) {
        $this->message(
            $message,
            'debug',
            $this->config->get('LOGGER.DEBUG_LOG_FILE') ??
                $this->config->get('LOGGER.LOG_FILE')
        );
    }
}
