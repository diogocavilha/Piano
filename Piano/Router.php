<?php

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 * @package Piano
 */

namespace Piano;

use InvalidArgumentException;

class Router
{
    private $regexDelimiter = '#';
    private $urlVar = ':';
    private $routes = [];
    private $matchedRoute = null;
    private $matchedRouteName = null;
    private $matchedRouteParams = null;
    private $searchEngineFriendly = false;

    public function addRoute($name, $routePath, array $args)
    {
        $params = null;

        foreach ($args as $pos => $arg) {
            if (is_array($arg)) {
                $params = $arg;
                unset($args[$pos]);
                break;
            }
        }

        $route['route'] = $routePath;
        $route += $args;

        if (!is_null($params)) {
            $route['params'] = $params;
        }

        $this->routes[$name] = $route;

        return $this;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getRoute($name = null)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    public function getMatchedRouteParams()
    {
        return $this->matchedRouteParams;
    }

    public function getMatchedRouteName()
    {
        return $this->routes[$this->matchedRouteName];
    }

    public function match($url = null)
    {
        $match = false;

        if (is_null($url)) {
            throw new InvalidArgumentException('Param url is expected.');
        }

        $urlPieces = explode('/', $url);

        foreach ($this->routes as $routeName => $route) {
            if (isset($route[0])) {
                $route['params'] = $route[0];
                unset($route[0]);
            }

            $match = false;
            $routePieces = explode('/', $route['route']);
            $urlVariables = [];
            $urlRegex = [];

            foreach ($routePieces as $pos => $segment) {
                if (substr($segment, 0, 1) == $this->urlVar) {
                    $urlVariables[$pos] = substr($segment, 1);

                    if (array_key_exists('params', $route)) {
                        $urlRegex[$pos] = $route['params'][$segment];
                    }
                } else {
                    $urlRegex[$pos] = $segment;
                }
            }

            $urlRegex = implode('/', $urlRegex);

            if (preg_match($this->regexDelimiter.'^'.$urlRegex.'$'.$this->regexDelimiter, $url)) {
                $params = [];

                foreach ($urlVariables as $varPos => $var) {
                    $params[$var] = $urlPieces[$varPos];
                }

                unset($route['route'], $route['params']);

                $this->matchedRoute = $route;
                $this->matchedRouteParams = $params;
                $this->matchedRouteName = $routeName;
                return true;
            }
        }

        return $match;
    }

    private function getParamsFrom($route)
    {
        $arrayUrlParams = [];

        if (array_key_exists(0, $route)) {
            $arrayUrlParams = $route[0];
        }

        return $arrayUrlParams;
    }

    public function getUrl($name = null, array $params = null)
    {
        if (is_null($name)) {
            throw new InvalidArgumentException('Param name is expected.');
        }

        if ($this->searchEngineFriendly) {
            return $this->getFriendlyUrlForRoute($name, $params);
        }

        return $this->getUrlForRoute($name, $params);
    }

    private function getFriendlyUrlForRoute($name, $params)
    {
        $route = $this->getRoute($name);
        $friendlyUrl = $route['route'];
        unset($route['route']);

        $arrayUrlParams = $this->getParamsFrom($route);
        unset($route[0]);

        $url = explode('/', $friendlyUrl);
        $urlRegex = $url;

        if (!is_null($params)) {
            foreach ($params as $key => $value) {
                if ($k = array_search(':'.$key, $url)) {
                    $url[$k] = $value;
                    $urlRegex[$k] = $arrayUrlParams[':'.$key];
                }
            }
        }

        $url = implode('/', $url);
        $urlRegex = implode('/', $urlRegex);

        // TODO
        // Maybe validate the URL pattern against $urlRegex?

        return $url;
    }

    private function getUrlForRoute($name, $params)
    {
        $route = $this->getRoute($name);
        unset($route['route']);

        $arrayUrlParams = $this->getParamsFrom($route);
        unset($route[0]);

        $url = '/' . implode('/', $route);
        $urlRegex = $url;

        $urlParams = [];
        $urlParamsRegex = [];
        if (!empty($arrayUrlParams)) {
            foreach ($arrayUrlParams as $key => $value) {
                $urlParams[] = substr($key, 1) . '/' . $params[substr($key, 1)];
                $urlParamsRegex[] = substr($key, 1) . '/' . $arrayUrlParams[$key];
            }
        }

        $url .= '/' . implode('/', $urlParams);
        $urlRegex .= '/' . implode('/', $urlParamsRegex);

        // TODO
        // Maybe validate the URL against $urlRegex?

        return $url;
    }

    public function enableSearchEngineFriendly($flag = true)
    {
        $this->searchEngineFriendly = $flag;
    }

    public function isSearchEngineFriendly()
    {
        return $this->searchEngineFriendly;
    }
}
