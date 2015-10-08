<?php

/**
 * Parse ini class.
 *
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano\Config;

use RuntimeException;

class Ini
{
    private $config;

    public function __construct($path)
    {
        if (is_null($path)) {
            throw new RuntimeException('Path cannot be null.');
        }

        if (!file_exists($path)) {
            throw new RuntimeException('Config file not found.');
        }

        $config = parse_ini_file($path, true, 2);

        $this->config = json_decode(json_encode($config));
    }

    public function getConfig()
    {
        return $this->config;
    }
}
