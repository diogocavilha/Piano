<?php

namespace Piano\Mvc;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Controller
{
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
        $this->application = $application;
        $this->view = new \Piano\Mvc\View($application);

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function redirect(string $url = null, array $args = null)
    {
        if (is_null($url)) {
            throw new \InvalidArgumentException('Param url is expected.');
        }

        $this->application->redirect($url, $args);
        return;
    }

    public function getDi() : \Piano\Container
    {
        return $this->application->getDi();
    }

    public function getParams() : array
    {
        return $this->application->getParams();
    }

    public function getParam(string $name, string $default = null) : string
    {
        return $this->application->getParam($name, $default);
    }
}
