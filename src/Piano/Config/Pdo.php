<?php

namespace Piano\Config;

class Pdo
{
    private $config;

    public function __construct($config = null)
    {
        $this->checkConfig($config);
        $this->config = (object) $config;
    }

    public function get()
    {
        $dsn = "{$this->config->dbAdapter}:host={$this->config->dbHost};dbname={$this->config->dbName};";
        return new \PDO($dsn, $this->config->dbUser, $this->config->dbPass);
    }

    private function checkConfig($config)
    {
        $runtimeExceptionMessage = "Invalid data access. Key %s is expected.";

        if (!is_array($config) || empty($config)) {
            throw new \RuntimeException('Invalid data access. Array is expected.');
        }

        if (!array_key_exists('dbAdapter', $config)) {
            throw new \RuntimeException(sprintf($runtimeExceptionMessage, 'dbAdapter'));
        }

        if (!array_key_exists('dbHost', $config)) {
            throw new \RuntimeException(sprintf($runtimeExceptionMessage, 'dbHost'));
        }

        if (!array_key_exists('dbName', $config)) {
            throw new \RuntimeException(sprintf($runtimeExceptionMessage, 'dbName'));
        }

        if (!array_key_exists('dbUser', $config)) {
            throw new \RuntimeException(sprintf($runtimeExceptionMessage, 'dbUser'));
        }

        if (!array_key_exists('dbPass', $config)) {
            throw new \RuntimeException(sprintf($runtimeExceptionMessage, 'dbPass'));
        }
    }
}
