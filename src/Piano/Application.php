<?php

declare(strict_types=1);

namespace Piano;

use \Piano\Container;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Application
{
    private $container;
    private $moduleName;
    private $controllerName;
    private $actionName;
    private $urlParams = [];

    public function __construct(Container $container)
    {
        if (!isset($container['config'])) {
            throw new \RuntimeException('Key "config" is missing');
        }

        if (!isset($container['router'])) {
            throw new \RuntimeException('Key "router" is missing');
        }

        $this->container = $container;

        $this->setUrl();
    }

    public function getDi() : Container
    {
        return $this->container;
    }

    public function getApplicationFolderName() : string
    {
        return $this->getDi()['config']->get('defaultDirectory');
    }

    public function getDefaultModuleName() : string
    {
        return $this->getDi()['config']->get('defaultModule');
    }

    public function getModuleName() : string
    {
        return $this->moduleName;
    }

    public function getControllerName() : string
    {
        return $this->controllerName;
    }

    public function getActionName() : string
    {
        return $this->actionName;
    }

    public function getParams() : array
    {
        return $this->urlParams;
    }

    public function getParam(string $name, string $default = null) : string
    {
        if (isset($this->urlParams[$name])) {
            return $this->urlParams[$name] ?? $default;
        }

        throw new \Exception("Key '$name' not found in array.");
    }

    /**
     * Sets the requested URL.
     *
     * In case the URL does not exist, sets the default URL to the default module.
     * @access public
     */
    public function setUrl(string $urlPath = '')
    {
        $router = $this->getDi()['router'];

        if (empty($urlPath)) {
            $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        if (!$router->match($urlPath)) {
            return $this->dispatchNotFoundRoute();
        }

        $routeFound = $router->getMatchedRoute();
        $this->moduleName = $routeFound['module'];
        $this->controllerName = $routeFound['controller'];
        $this->actionName = $routeFound['action'];
        $this->urlParams = $router->getMatchedRouteParams();
    }

    protected function dispatchNotFoundRoute()
    {
        $router = $this->getDi()['router'];
        $route404 = $router->getRoute('error404');

        if (is_null($route404)) {
            die('404 - Route not found!'); // @codeCoverageIgnore
        }

        $this->moduleName = $route404['module'];
        $this->controllerName = sprintf(
            '%sController',
            ucfirst($route404['controller'])
        );
        $this->actionName = $route404['action'];

        return;
    }

    public function redirect(string $urlPath = '', array $args = [])
    {
        if (empty($urlPath)) {
            throw new \InvalidArgumentException('Param url is expected.');
        }

        $router = $this->getDi()['router'];
        $route = $router->getRoute($urlPath);

        if (is_null($route)) {
            return;
        }

        if ($router->isSearchEngineFriendly() && empty($args)) {
            $url = sprintf('//%s%s', $_SERVER['HTTP_HOST'], $route['route']);
            $this->header("Location: $url");
            return;
        }

        if ($router->isSearchEngineFriendly() && !empty($args)) {
            $routePieces = explode('/', $route['route']);
            $urlPattern = [];
            $url = [];
            foreach ($routePieces as $pos => $segment) {
                if ($router->isVar($segment) && !isset($args[$router->getVar()])) {
                    throw new \Exception('Invalid parameters');
                }

                if ($router->isVar($segment)) {
                    $urlPattern[$pos] = $route[0][$segment];
                    $url[] = $args[$router->getVar()];
                    continue;
                }

                $url[] = $segment;
                $urlPattern[$pos] = $segment;
            }

            $urlPattern = implode('/', $urlPattern);
            $url = implode('/', $url);
            $url = sprintf('//%s%s', $_SERVER['HTTP_HOST'], $url);
            $this->header("Location: $url");
            return;
        }

        if (!$router->isSearchEngineFriendly() && empty($args)) {
            $url = sprintf(
                '//%s/%s/%s/%s',
                $_SERVER['HTTP_HOST'],
                $route['module'],
                $route['controller'],
                $route['action']
            );
            $this->header("Location: $url");
            return;
        }

        if (!$router->isSearchEngineFriendly() && !empty($args)) {
            $routePieces = explode('/', $route['route']);

            $url = $urlPattern = [
                $_SERVER['HTTP_HOST'],
                $route['module'],
                $route['controller'],
                $route['action']
            ];

            foreach ($routePieces as $pos => $segment) {
                if ($router->isVar($segment) && !isset($args[$router->getVar()])) {
                    throw new \Exception('Invalid parameters');
                }

                if ($router->isVar($segment)) {
                    $urlPattern[] = $router->getVar();
                    $urlPattern[] = $route[0][$segment];
                    $url[] = $router->getVar();
                    $url[] = $args[$router->getVar()];
                    continue;
                }
            }

            $urlPattern = '//' . implode('/', $urlPattern);
            $url = '//' . implode('/', $url);

            $this->header("Location: $url");
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function header(string $location)
    {
        header($location);
    }

    private function checkModulePath()
    {
        $modulePath = sprintf(
            '../src/%s/modules/%s',
            $this->getApplicationFolderName(),
            $this->getModuleName()
        );

        if (!file_exists($modulePath)) {
            throw new \Exception(sprintf('Module not found: %s', $this->getModuleName()));
        }
    }

    private function requireController()
    {
        $controllerPath = sprintf(
            '../src/%s/modules/%s/controllers/%s.php',
            $this->getApplicationFolderName(),
            $this->getModuleName(),
            $this->getControllerName()
        );

        if (!file_exists($controllerPath)) {
            throw new \Exception(sprintf('Controller not found: %s', $this->getControllerName()));
        }

        require_once $controllerPath;
    }

    public function run()
    {
        $this->checkModulePath();
        $this->requireController();

        $declaredClasses = get_declared_classes();
        $namespaceController = end($declaredClasses);

        $action = sprintf('%sAction', $this->getActionName());
        $controller = new $namespaceController($this);

        if (!method_exists($controller, $action)) {
            throw new \Exception('Action not found: %s', $this->getActionName());
        }

        $controller->$action();
    }
}
