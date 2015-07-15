<?php

/**
 * Configuration class management.
 *
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano;

use Exception;
use InvalidArgumentException;

class Config
{
    private $config = [];

    /**
     * Returns the system's array configuration.
     * @return $config
     */
    public function getArray($key = null)
    {
        if (!is_null($key)) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }

            throw new Exception("{$key} does not exist in configuration array.");
        }

        return $this->config;
    }

    /**
     * Returns the system's standard class configuration.
     *
     * @return $config
     */
    public function getObject($key = null)
    {
        if (!is_null($key)) {
            if (isset($this->config[$key])) {
                return (object) $this->config[$key];
            }

            throw new Exception("{$key} does not exist in configuration array.");
        }

        return (object) $this->config;
    }

    /**
     * Returns a configuration value.
     *
     * @return $config
     */
    public function get($key = null)
    {
        return $this->getArray($key);
    }

    public function setDefaultModule($name = null)
    {
        if (is_null($name)) {
            throw new InvalidArgumentException('Param name is expected.');
        }

        $this->config['default_module'] = $name;
        return $this;
    }

    public function setLayoutPerModule(array $layoutModule)
    {
        $this->config['layout_module'] = $layoutModule;
        return $this;
    }

    public function setApplicationFolder($name = null)
    {
        if (is_null($name)) {
            throw new InvalidArgumentException('Param name is expected.');
        }

        $this->config['application_folder'] = $name;
        return $this;
    }
}
