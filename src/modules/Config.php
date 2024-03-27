<?php

require_once('./modules/Logger.php');

class Config {
    private mixed $config;
    private Logger $logger;
    private array $logs = [];
    private mixed $configSchema;

    public function __construct(mixed $rawConfig) {
        $this->configSchema = require_once('./modules/ConfigSchema.php');
        $this->config = $rawConfig;

        $this->config = array_merge($this->config, parse_ini_file('../.env', true));

        // Ce fichier étant exécuté à chaque requête, il est préférable de ne pas valider le schéma à chaque fois
        if ($this->get('ENVIRONMENT') === 'development' || $this->get('APP_ENV') === 'configuration') {
            $this->validate();
        }

        $this->logger = new Logger($this, 'Config');

        $this->showLogs();
        if (count(array_filter($this->logs, fn ($log) => $log['type'] === 'error')) > 0) {
            throw new Error('Configuration errors detected. Please check the logs for more information.');
        }
    }

    public function get(string $key): mixed {
        $current = $this->config;
        foreach (explode('.', $key) as $key) {
            if (!isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }
        return $current;
    }

    private function validate() {
        $this->validateSchema($this->configSchema, $this->config, '');
    }

    private function validateSchema($schema, $data, $path) {
        foreach ($schema as $key => $type) {
            $optionnal = false;
            if (gettype($type) === "string") {
                if (substr_compare($type, '?', -1, 1) == 0) {
                    $optionnal = true;
                }
            } else {
                if (array_key_exists('optionnal', $type) && $type['optionnal']) {
                    $optionnal = true;
                }
            }

            if (!isset($data[$key])) {
                if (!$optionnal) {
                    $this->addLog('error', "Missing configuration key: $path.$key");
                }
                continue;
            }

            if (gettype($type) === 'array') {
                if ($type['type'] === 'list') {
                    for ($i = 0; $i < count($data[$key]); $i++) {
                        $this->validateSchema($type['schema'], $data[$key][$i], "$path.$key.$i");
                    }
                    continue;
                }

                if ($type['type'] === 'array') {
                    $this->validateSchema($type['schema'], $data[$key], "$path.$key");
                    continue;
                }

                if ($type['type'] === 'directory') {
                    if (gettype($data[$key]) !== 'string') {
                        $this->addLog(
                            'error',
                            "Invalid type for configuration key: $path.$key. Expected string, got " . gettype($data[$key])
                        );
                        continue;
                    }

                    if ($type['checkValidity'] && !is_dir($data[$key])) {
                        if ($type['autoCreate']) {
                            if (mkdir($data[$key], 0777, true)) {
                                $this->addLog('warning', "Invalid path for configuration key: $path.$key. Directory was created automatically");
                            } else {
                                $this->addLog('error', "Invalid path for configuration key: $path.$key. Directory could not be created automatically");
                            }
                            continue;
                        }

                        $this->addLog(
                            'error',
                            "Invalid path for configuration key: $path.$key. Directory does not exist"
                        );
                    }
                    continue;
                }

                if ($type['type'] === 'file') {
                    if (gettype($data[$key]) !== 'string') {
                        $this->addLog(
                            'error',
                            "Invalid type for configuration key: $path.$key. Expected string, got " . gettype($data[$key])
                        );
                        continue;
                    }

                    if ($type['checkDirectoryValidity']) {
                        $directory = dirname($data[$key]);

                        if (!is_dir($directory)) {
                            if ($type['autoCreateDirectory']) {
                                if (mkdir($directory, 0777, true)) {
                                    $this->addLog('warning', "Invalid path for configuration key: $path.$key. Directory was created automatically");
                                } else {
                                    $this->addLog('error', "Invalid path for configuration key: $path.$key. Directory could not be created automatically");
                                }
                                continue;
                            }

                            $this->addLog(
                                'error',
                                "Invalid path for configuration key: $path.$key. Directory does not exist"
                            );
                            continue;
                        }
                    }

                    if ($type['checkValidity'] && !is_file($data[$key])) {
                        $this->addLog('error', "Invalid path for configuration key: $path.$key. File does not exist");
                    }
                    continue;
                }

                $this->addLog('error', "Invalid schema for configuration key: $path.$key");

                continue;
            }

            if (gettype($data[$key]) !== $type) {
                $this->addLog('error', "Invalid type for configuration key: $path.$key. Expected $type, got " . gettype($data[$key]));
            }
        }
    }

    private function addLog(string $type, string $messages) {
        $this->logs[] = [
            'type' => $type,
            'message' => $messages
        ];
    }

    private function showLogs() {
        foreach ($this->logs as $log) {
            if ($log['type'] === 'error') {
                $this->logger->error($log['message']);
                continue;
            }

            if ($log['type'] === 'warning') {
                $this->logger->warning($log['message']);
                continue;
            }
        }
    }
}
