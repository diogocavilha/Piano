<?php

namespace Piano\Mvc;

use Piano\Mvc\View;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Controller
{
    /**
     * @var Piano\Config
     */
    private $config;

    /**
     * @var Piano\Application
     */
    private $application;

    /**
     * @var Piano\Mvc\View
     */
    protected $view;

    public function __construct(\Piano\Application $application)
    {
        $this->config = $application->getConfig();
        $this->application = $application;
        $this->view = new View($application);

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    public function getParams()
    {
        return $this->application->getParams();
    }

    public function getParam($name = null, $default = null)
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('Param name is expected.');
        }

        return $this->application->getParam($name, $default);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function redirect($url = null, array $args = null)
    {
        if (is_null($url)) {
            throw new \InvalidArgumentException('Param url is expected.');
        }

        $this->application->redirect($url, $args);
        exit();
    }

    public function getApplication()
    {
        return $this->application;
    }
}
