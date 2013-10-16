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
            list($module, $controller, $action) = $route;
            if (isset($route[3])) {
                $controller = $controller.'/'.$action;
                $action = $route[3] ? $route[3] : F::$config->router['default_action'];
            }
            $route = $this->_route($module, $controller, $action);
        }

        return $route;
    }

    /**
     * 
     * @return string
     */
    private function _removeUriSuffix($segment)
    {
        return basename($segment, '.'.F::$config->router['url_suffix']);
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
            list($module, $controller, $action) = $customRoute;
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

        if (!$customRoute 
            && is_dir(F::getApp()->getModulesDirectory().$module
                .'/'.F::getApp()->getControllersDirectoryName().$controller))
        {
            $tmpController = null;
            $segmentActionIndex = 3;
            $firstSegment = F::$uri->getSegment(1);
            if ($firstSegment && in_array($firstSegment, $this->_routeModules)) {
                $tmpController = F::$uri->getSegment($segmentActionIndex);
                $segmentActionIndex = 4;
            } else {
                $tmpController = F::$uri->getSegment($segmentActionIndex - 1);
            }
            if (!$tmpController) {
                $tmpController = F::$config->router['default_controller'];
            }
            $controller = $controller.'/'.$tmpController;
            $action = F::$uri->getSegment($segmentActionIndex);
            if (!$action) {
                $action = F::$config->router['default_action'];
            }
            unset($segmentActionIndex, $firstSegment, $tmpController);
        }

        $route = $this->_route($module, $controller, $this->_removeUriSuffix($action));
        if ($route) {
            if (!$customRoute) {
                $this->_setActionParams($route, $validUriForParams);
                if (!$validUriForParams) {
                    return null;
                } elseif (!empty(F::$config->router['url_suffix'])) {
                    $matchActionSuffix = (pathinfo($action, PATHINFO_EXTENSION) === F::$config->router['url_suffix']);
                    $matchUriSuffix = (F::$uri->suffix === F::$config->router['url_suffix']);
                    if ((!$route->getActionParams() && $action !== F::$config->router['default_action'] && !$matchActionSuffix)
                        || ($route->getActionParams() && ($matchActionSuffix || !$matchUriSuffix))) {
                        return null;
                    }
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
            if (F::$request->hasSubmodule()) {
                $indexStart = 4;
            }
            if ($params) {
                if ($firstSegment) {
                    if (in_array($firstSegment, $this->_routeModules)) {
                        $indexStart = 5;
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

                if ($actionParams && !empty(F::$config->router['url_suffix'])) {
                    $lastIndex = count($actionParams) - 1;
                    $actionParams[$lastIndex] = $this->_removeUriSuffix($actionParams[$lastIndex]);
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
                    $indexStart = 5;
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