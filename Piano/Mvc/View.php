<?php

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano\Mvc;

use InvalidArgumentException;

class View
{
    /**
     * @var Piano\Application
     */
    private $application;


    private $disableLayout = false;
    private $layout = null;

    /**
     * @var Piano\Helpers\Config
     */
    private $config;

    private $jsFilesPath = [];
    private $cssFilesPath = [];
    private $vars = [];

    public function __construct(\Piano\Application $application)
    {
        $this->application = $application;
        $this->config = $application->getConfig();
    }

    public function addVar($variable, $value = null)
    {
        $this->vars[$variable] = $value;
    }

    public function setVars(array $variables = array())
    {
        $this->vars = $variables;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function render($viewName = null, array $vars = null)
    {
        if (is_null($viewName)) {
            throw new InvalidArgumentException('View name is expected.');
        }

        $controller = strtolower(preg_replace('/Controller$/', '', $this->application->getControllerName()));
        $viewPath = '/modules/' . $this->application->getModuleName() . '/views/' . $controller . '/' . $viewName;
        $layoutPath = '/layouts/' . $this->getPathLayout();

        if (!file_exists($this->getCompleteViewPath($layoutPath))) {
            die('Layout ' . $this->getPathLayout() . '.phtml does not exist.'); // @codeCoverageIgnore
        }

        if (is_array($vars) && count($vars) > 0) {
            $vars['view'] = $this->getCompleteViewPath($viewPath);
        } else {
            $vars = ['view' => $this->getCompleteViewPath($viewPath)];
        }

        $vars = array_merge($vars, $this->vars);

        $this->vars = $vars;

        if ($this->disableLayout) {
            $this->partial($viewPath, $vars);
        } else {
            $this->partial($layoutPath, $vars);
        }
    }

    /**
     * Adds a partial to be rendered
     * @param string $partialName
     * @param array $vars
     */
    public function partial($name = null, array $vars = null)
    {
        if (is_null($name)) {
            throw new InvalidArgumentException('Partial name is expected.');
        }

        $partialVars = $this->vars;

        if (is_array($vars) && count($vars) > 0) {
            $partialVars = array_merge($this->vars, $vars);
            extract($partialVars); // @codeCoverageIgnore
        }

        require_once $this->getCompleteViewPath($name); // @codeCoverageIgnore
    }

    /**
     * @param boolean $bool
     */
    public function disableLayout($bool = true)
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

        $modulesLayout = $this->application->getModulesLayout();

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
    public function getCompleteViewPath($path)
    {
        return '../src/' . $this->application->getApplicationFolderName() . $path . '.phtml';
    }

    public function url($routeName = null, array $params = null)
    {
        if (is_null($routeName)) {
            throw new InvalidArgumentException('Param route name is expected.');
        }

        return $this->application->getRouter()->getUrl($routeName, $params);
    }

    public function addJs($jsFilePath = null)
    {
        if (!is_null($jsFilePath)) {
            $this->jsFilesPath[] = $jsFilePath;
        }

        return $this;
    }

    public function setJs(array $jsFilesPath)
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

    public function addCss($cssFilePath = null)
    {
        if (!is_null($cssFilePath)) {
            $this->cssFilesPath[] = $cssFilePath;
        }

        return $this;
    }

    public function setCss(array $cssFilesPath)
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
