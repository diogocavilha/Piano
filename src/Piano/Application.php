<?php

namespace Piano;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Application
{
    /**
     * @var Piano\Config\Ini $config
     */
    private $config;
    private $actionName;
    private $controllerName;
    private $defaultModuleName;
    private $moduleName;
    private $modulesLayout = [];
    private $routes;
    private $url;
    private $urlParams;
    private $urlPieces = [];

    /**
     * @var Piano\Route $route
     */
    private $router;

    public function __construct(\Piano\Config\Ini $config, \Piano\Router $router)
    {
        $this->config = $config;
        $this->router = $router;

        $this->setUrl();
    }

    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return Piano\Config\Ini
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getApplicationFolderName()
    {
        $config = $this->config->get();
        return $config['defaultDirectory'];
    }

    public function getDefaultModuleName()
    {
        $config = $this->config->get();
        return $config['defaultModule'];
    }

    /**
     * Sets the requested URL.
     *
     * In case the URL does not exist, sets the default URL to the default module.
     * @access public
     */
    public function setUrl($urlPath = null, array $args = null)
    {
        if (is_null($urlPath)) {
            $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        if ($this->router->isSearchEngineFriendly()) {
            if ($this->router->match($urlPath)) {
                $routeFound = $this->router->getMatchedRoute();

                $this->moduleName = $routeFound['module'];
                $this->controllerName = ucfirst($routeFound['controller']) . 'Controller';
                $this->actionName = $routeFound['action'];
                $this->urlParams = $this->router->getMatchedRouteParams();

                return;
            }

            return $this->dispatchRouteDefault();
        }

        if ($urlPath == '/') {
            $config = $this->config->get();
            $this->moduleName = $config['defaultModule'];
            $this->controllerName = 'IndexController';
            $this->actionName = 'index';

            return;
        }

        $arrayUrl = explode('/', $urlPath);

        if (count($arrayUrl) <= 3) {
            return $this->dispatchRouteDefault();
        }

        $this->moduleName = $arrayUrl[1];
        $this->controllerName = ucfirst($arrayUrl[2]) . 'Controller';
        $this->actionName = $arrayUrl[3];

        unset($arrayUrl[0], $arrayUrl[1], $arrayUrl[2], $arrayUrl[3]);

        if (is_null($args)) {
            $args = [];
            foreach ($arrayUrl as $key => $value) {
                if ($key == 0 || $key % 2 == 0) {
                    $args[$value] = (!isset($arrayUrl[$key+1]) || empty($arrayUrl[$key+1])) ? '' : $arrayUrl[$key+1];
                }
            }
        }

        $this->urlParams = $args;
    }

    /**
     * @access public
     */
    public function redirect($urlPath = null, array $args = null)
    {
        if (is_null($urlPath)) {
            throw new \InvalidArgumentException('Param url is expected.');
        }

        $arrayUrl = explode('/', $urlPath);

        if (count($arrayUrl) <= 3) {
            return $this->dispatchRouteDefault();
        }

        $this->moduleName = $arrayUrl[1];
        $this->controllerName = ucfirst($arrayUrl[2]) . 'Controller';
        $this->actionName = $arrayUrl[3];

        $module = $arrayUrl[1];
        $controller = $arrayUrl[2];
        $action = $arrayUrl[3];

        unset($arrayUrl[0], $arrayUrl[1], $arrayUrl[2], $arrayUrl[3]);

        if ($this->router->isSearchEngineFriendly()) {
            if (is_null($args)) {
                $args = [];
                foreach ($arrayUrl as $key => $value) {
                    if ($key == 0 || $key % 2 == 0) {
                        $args[$value] = (!isset($arrayUrl[$key+1]) || empty($arrayUrl[$key+1])) ? '' : $arrayUrl[$key+1];
                    }
                }
            }

            $this->urlParams = $args;

            $allRoutes = $this->router->getRoutes();

            foreach ($allRoutes as $route) {
                if ($route['module'] == $module && $route['controller'] == $controller && $route['action'] == $action) {
                    $friendlyUrl = $route['route'];
                    $slashParams = $this->getSlashUrlParams($args);

                    header("Location: //{$_SERVER['HTTP_HOST']}{$friendlyUrl}{$slashParams}");

                    return;
                }
            }
        }

        $this->urlParams = $args;
        $slashParams = $this->getSlashUrlParams($args);

        header("Location: //{$_SERVER['HTTP_HOST']}{$urlPath}{$slashParams}");
    }

    private function getSlashUrlParams($args)
    {
        $slashParams = '';
        if (!is_null($args) && count($args) > 0) {
            foreach ($args as $key => $value) {
                $slashParams .= '/'. $key . '/' . $value;
            }
        }

        return $slashParams;
    }

    private function dispatchRouteDefault()
    {
        $route_404 = $this->router->getRoute('error_404');

        if (is_null($route_404)) {
            die('404 - Route not found!'); // @codeCoverageIgnore
        }

        $this->moduleName = $route_404['module'];
        $this->controllerName = ucfirst($route_404['controller']) . 'Controller';
        $this->actionName = $route_404['action'];

        return;
    }

    /**
     * Returns the requested module's name.
     * @return string $this->moduleName
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Returns the requested controller's name.
     * @return string $this->controllerName
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * Returns the requested action's name.
     * @return string $this->actionName
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Gets the URL params.
     * @return array $this->urlParams
     */
    public function getParams()
    {
        return $this->urlParams;
    }

    public function registerModulesLayout(array $modulesLayouts)
    {
        $this->modulesLayouts = $modulesLayouts;
        return $this;
    }

    public function getModulesLayout()
    {
        return $this->modulesLayouts;
    }

    /**
     * Returns a value based on its URL key.
     * The default value is assumed in case the key does not exist.
     *
     * @param string $name
     * @param string $default
     */
    public function getParam($name = null, $default = null)
    {
        if (is_null($name)) {
            return false;
        }

        if (isset($this->urlParams[$name])) {
            $value = (!is_null($this->urlParams[$name])) ? $this->urlParams[$name] : $default;
            return $value;
        }

        throw new \Exception("Key '$name' not found in array.");
    }

    public function run()
    {
        $modulePath = '../src/' . $this->getApplicationFolderName() . '/modules/' . $this->getModuleName();
        $controllerPath = '../src/' . $this->getApplicationFolderName() . '/modules/' . $this->getModuleName() . '/controllers/' . $this->getControllerName() . '.php';

        if (!file_exists($modulePath)) {
            $this->errorStop("Module <strong>{$this->getModuleName()}</strong> not found.");
        }

        if (!file_exists($controllerPath)) {
            $this->errorStop("Controller <strong>{$this->getControllerName()}</strong> not found.");
        }

        $controller = '\\'
                    . $this->getApplicationFolderName()
                    . '\\modules\\'
                    . $this->getModuleName()
                    . '\\controllers\\'
                    . $this->getControllerName();

        $action = $this->getActionName() . 'Action';

        $controller = new $controller($this);

        if (!method_exists($controller, $action)) {
            $this->errorStop("Action <strong>{$this->getActionName()}Action</strong> not found.");
        }

        $controller->$action();
    }

    /**
     * Shows internal errors while trying to load module/controller/action and it doesn't exist.
     */
    private function errorStop($message)
    {
        echo $message;
        echo '<pre>';
        print_r([
            'module' => $this->getModuleName(),
            'controller' => $this->getControllerName(),
            'action' => $this->getActionName() . 'Action',
        ]);

        exit();
    }
}