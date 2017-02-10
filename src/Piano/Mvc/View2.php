<?php

declare(strict_types=1);

namespace Piano\Mvc;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class View2
{
    /**
     * @var Piano\Application
     */
    private $application;

    private $disableLayout = false;
    private $layout = null;
    private $jsFilesPath = [];
    private $cssFilesPath = [];
    private $vars = [];

    public function __construct(\Piano\Application $application)
    {
        $this->application = $application;
    }

    public function addVar(string $variable, $value = null)
    {
        $this->vars[$variable] = $value;
    }

    public function setVars(array $variables = [])
    {
        $this->vars = $variables;
    }

    public function getVars() : array
    {
        return $this->vars;
    }

    public function render($viewName = null, array $vars = null)
    {
        if (is_null($viewName)) {
            throw new \InvalidArgumentException('View name is expected.');
        }

        $controller = strtolower(preg_replace('/Controller$/', '', $this->application->getControllerName()));
        $viewPath = '/modules/' . $this->application->getModuleName() . '/views/' . $controller . '/' . $viewName;
        $layoutPath = '/layouts/' . $this->getPathLayout();

        if (!file_exists($this->getCompleteViewPath($layoutPath))) {
            throw new \RuntimeException(sprintf('Layout not found: %s', $this->getPathLayout()));
        }

        $vars['view'] = $this->getCompleteViewPath($viewPath);
        if (!is_array($vars) || count($vars) == 0) {
            $vars = ['view' => $this->getCompleteViewPath($viewPath)];
        }

        $this->vars = array_merge($vars, $this->vars);

        $load = $layoutPath;
        if ($this->disableLayout) {
            $load = $viewPath;
        }

        $this->partial($load, $this->vars);
    }

    /**
     * Adds a partial to be rendered
     * @param string $partialName
     * @param array $vars
     */
    public function partial($name = null, array $vars = [])
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('Partial name is expected.');
        }

        $partialVars = array_merge($this->vars, $vars); // @codeCoverageIgnore

        if (is_array($partialVars) && count($partialVars) > 0) { // @codeCoverageIgnore
            extract($partialVars); // @codeCoverageIgnore
        }

        require_once $this->getCompleteViewPath($name); // @codeCoverageIgnore
    }

    /**
     * @param boolean $bool
     */
    public function disableLayout(bool $bool = true)
    {
        $this->disableLayout = $bool;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    private function getPathLayout()
    {
        if (!is_null($this->layout)) {
            return $this->layout;
        }

        $modulesLayout = $this->application->getDi()['modulesLayout'];

        $layouts = array_keys($modulesLayout);
        $i = 0;

        foreach ($modulesLayout as $modules) {
            if (in_array($this->application->getModuleName(), $modules)) {
                return $layouts[$i];
            }
            ++$i;
        }

        return reset($layouts);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getCompleteViewPath(string $path) : string
    {
        return sprintf(
            '../src/%s%s.phtml',
            $this->application->getApplicationFolderName(),
            $path
        );
    }

    public function url(string $routeName = null, array $params = null) : string
    {
        if (is_null($routeName)) {
            throw new \InvalidArgumentException('A route name is expected.');
        }

        return $this->application->getDi()['router']->getUrl($routeName, $params);
    }

    public function addJs($jsFilePath = null) : \Piano\Mvc\View2
    {
        if (!is_null($jsFilePath)) {
            $this->jsFilesPath[] = $jsFilePath;
        }

        return $this;
    }

    public function setJs(array $jsFilesPath) : \Piano\Mvc\View2
    {
        if (!empty($jsFilesPath)) {
            $this->jsFilesPath = $jsFilesPath;
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function loadJs()
    {
        foreach ($this->jsFilesPath as $jsFilePath) {
            echo "<script src=\"$jsFilePath\"></script>";
        }
    }

    public function addCss($cssFilePath = null) : \Piano\Mvc\View2
    {
        if (!is_null($cssFilePath)) {
            $this->cssFilesPath[] = $cssFilePath;
        }

        return $this;
    }

    public function setCss(array $cssFilesPath) : \Piano\Mvc\View2
    {
        if (!empty($cssFilesPath)) {
            $this->cssFilesPath = $cssFilesPath;
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function loadCss()
    {
        foreach ($this->cssFilesPath as $cssFilePath) {
            echo "<link href=\"$cssFilePath\" rel=\"stylesheet\">";
        }
    }
}
