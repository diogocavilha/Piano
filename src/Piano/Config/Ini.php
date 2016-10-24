<?php

declare(strict_types=1);

namespace Piano\Config;

/**
 * Parse ini class.
 *
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Ini
{
    private $config;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Config file not found.');
        }

        $this->config = parse_ini_file($path, true);
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function toArray() : array
    {
        return $this->config;
    }
}
