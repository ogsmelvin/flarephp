<?php

namespace Flare\Application\Router\Adapter;

use Flare\Application\Router\Adapter;
use Flare\Application\Router\Route;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Page extends Adapter
{
    /**
     * 
     * @var array
     */
    private $_routes = array();

    /**
     * 
     * @param string $url
     * @param string $method
     * @return \Flare\Application\Router
     */
    public function addRoute($url, $method)
    {
        $this->_routes[trim($url, '/')] = $method;
        return $this;
    }

    /**
     * 
     * @param array $routes
     * @return \Flare\Application\Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $key => $route) {
            $this->addRoute($key, $route);
        }
        return $this;
    }

    /**
     * 
     * @return \Flare\Application\Router
     */
    public function clearRoutesList()
    {
        $this->_routes = array();
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getRoutesList()
    {
        return $this->_routes;
    }

    /**
     * 
     * @return string|null
     */
    private function _getMatchedCustomRoute()
    {
        $uri = trim((string) F::$uri, '/');
        if (isset($this->_routes[$uri])) {
            return $this->_routes[$uri];
        } else {
            foreach ($this->_routes as $key => $class) {
                if (preg_match('#^'.$key.'$#', $uri)) {
                    return $class;
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return \Flare\Application\Router\Route|null
     */
    public function getMatchedCustomRoute()
    {
        $route = $this->_getMatchedCustomRoute();
        if ($route) {
            // list($route, $params) = explode(':', $route, 2);
            // if ($params) {
            //   $params = explode(',', ltrim(rtrim($params, ')'), '('));
            // }
            list($module, $controller, $action) = explode('.', $route, 3);
            $route = $this->_route($module, $controller, $action);
        }

        return $route;
    }

    /**
     * 
     * @return \Flare\Application\Router\Route|null
     */
    public function getRoute()
    {
        $customRoute = $this->_getMatchedCustomRoute();
        $module = F::$uri->getSegment(1);
        $controller = F::$uri->getSegment(2);
        $action = F::$uri->getSegment(3);

        if ($customRoute) {
            list($module, $controller, $action) = explode('.', $customRoute, 3);
        } elseif ($module === null) {
            $module = F::$config->router['default_module'];
            $action = F::$config->router['default_action'];
            $controller = F::$config->router['default_controller'];
        } elseif (!in_array($module, $this->_routeModules)) {
            $action = $controller;
            $controller = $module;
            $module = F::$config->router['default_module'];
        }

        $controller = $controller === null ? F::$config->router['default_controller'] : $controller;
        $action = $action === null ? F::$config->router['default_action'] : $action;

        $route = $this->_route($module, $controller, $action);
        if ($route) {
            if (!$customRoute) {
                $this->_setActionParams($route, $validUriForParams);
                if (!$validUriForParams) {
                    return null;
                }
            } elseif (!$route->getAction()->exists()) {
                return null;
            }
        }

        return $route;
    }

    /**
     * 
     * @param \Flare\Application\Router\Route $route
     * @param boolean $validUriForParams
     * @return void
     */
    private function _setActionParams(Route &$route, &$validUriForParams = null)
    {
        $actionParams = array();
        if (!$route->getAction()->exists()) {
            $validUriForParams = false;
            return;
        } else {

            $segmentCount = F::$uri->getSegmentCount();
            $firstSegment = F::$uri->getSegment(1);
            if (!$firstSegment) $firstSegment = F::$config->router['default_module'];
            $params = $route->getAction()->getParameters();
            $indexStart = 3;
            if ($params) {
                if ($firstSegment) {
                    if (in_array($firstSegment, $this->_routeModules)) {
                        $indexStart = 4;
                    }
                } else {
                    $validUriForParams = false;
                    return;
                }
                if (!$params[0]->isOptional() && $segmentCount < $indexStart) {
                    $validUriForParams = false;
                    return;
                }

                $i = $indexStart;
                foreach ($params as $param) {
                    if ($i <= $segmentCount) {
                        if ($segmentValue = F::$uri->getSegment($i++)) {
                            $actionParams[] = $segmentValue;
                        }
                    }
                }
                
                $segmentParamsCount = ($segmentCount - $indexStart) + 1;
                $segmentParamsCount = $segmentParamsCount < 0 ? 1 : $segmentParamsCount;
                if ($segmentParamsCount > $route->getAction()->getNumberOfParameters()
                    || $segmentParamsCount < $route->getAction()->getNumberOfRequiredParameters())
                {
                    $validUriForParams = false;
                    return;
                }
            } else {
                if ($firstSegment && in_array($firstSegment, $this->_routeModules)) {
                    $indexStart = 4;
                }
                if ($segmentCount >= $indexStart) {
                    $validUriForParams = false;
                    return;
                }
            }
            unset($params, $indexStart, $segmentCount, $firstSegment);
        }
        
        $validUriForParams = true;
        $route->setActionParams($actionParams);
    }
}