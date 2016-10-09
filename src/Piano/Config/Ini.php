<?php

/**
 * Parse ini class.
 *
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano\Config;

class Ini
{
    private $config;

    public function __construct($path = null)
    {
        if (is_null($path)) {
            throw new \RuntimeException('Path cannot be null.');
        }

        if (!file_exists($path)) {
            throw new \RuntimeException('Config file not found.');
        }

        $this->config = parse_ini_file($path, true);
    }

    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        if (!array_key_exists($key, $this->config)) {
            return null;
        }

        return $this->config[$key];
    }
}
