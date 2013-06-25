<?php

namespace FPHP\Application;

use FPHP\Application\Router\Route\Action;
use FPHP\Application\Http\Request;
use FPHP\Application\Router\Route;
use FPHP\Fphp as F;

/**
 * 
 * @author anthony
 * 
 */
class Router
{
    /**
     * 
     * @var \FPHP\Application\Router\Route
     */
    private $_currentRoute = null;

    /**
     * 
     * @var array
     */
    private $_routes = array();

    /**
     * 
     * @var array
     */
    private $_routeModules = array();

    /**
     * 
     * @param array $routes
     */
    public function __construct(array $routes = array())
    {
        $this->addRoutes($routes);
    }

    /**
     * 
     * @param array $modules
     * @return \FPHP\Application\Router
     */
    public function setRoutingModules($modules)
    {
        $this->_routeModules = $modules;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getRoutingModules()
    {
        return $this->_routeModules;
    }

    /**
     * 
     * @param string $url
     * @param string $method
     * @return \FPHP\Application\Router
     */
    public function addRoute($url, $method)
    {
        $this->_routes[$url] = $method;
        return $this;
    }

    /**
     * 
     * @param array $routes
     * @return \FPHP\Application\Router
     */
    public function addRoutes(array $routes)
    {
        foreach($routes as $key => $route){
            $this->addRoute($key, $route);
        }
        return $this;
    }

    /**
     * 
     * @return string|null
     */
    private function _getMatchedCustomRoute()
    {
        $route = null;
        $uri = (string) F::$uri;
        if(isset($this->_routes[$uri])){
            $route = $this->_routes[$uri];
        } else {

        }
        return $route;
    }

    /**
     * 
     * @return \FPHP\Application\Router\Route|null
     */
    public function getMatchedCustomRoute()
    {
        if($this->_currentRoute){
            return $this->_currentRoute;
        }

        $route = $this->_getMatchedCustomRoute();
        if($route){
            list($module, $controller, $action) = explode('.', $route);
            $route = $this->_route($module, $controller, $action);
            if($route){
                $this->_currentRoute = $route;
            }
        }

        return $this->_currentRoute;
    }

    /**
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return \FPHP\Application\Router\Route
     */
    private function _route($module, $controller, $action)
    {
        $request = new Request();
        $request->setModule($module)
            ->setController($controller)
            ->setAction($action);

        $path = F::mvc()->getModulesDirectory()
            .$request->getModule()
            .'/'
            .F::mvc()->getControllersDirectory()
            .strtolower(urldecode($request->getController()))
            .'.php';
        if(!file_exists($path)){
            return null;
        }

        require F::mvc()->getModulesDirectory().$request->getModule().'/bootstrap.php';
        require $path;
        
        $controller = ucwords($request->getModule())."\\Controllers\\".$request->getControllerClassName();
        $route = new Route();
        $route->setModule($request->getModule());
        $route->setController(new $controller($request, F::$response));
        $route->setAction(new Action($route->getController(), $request->getActionMethodName()));

        return $route;
    }

    /**
     * 
     * @return \FPHP\Application\Router\Route|null
     */
    public function getRoute()
    {
        if($this->_currentRoute){
            return $this->_currentRoute;
        }

        $customRoute = $this->_getMatchedCustomRoute();
        $module = F::$uri->getSegment(1);
        $controller = F::$uri->getSegment(2);
        $action = F::$uri->getSegment(3);

        if($customRoute){
            list($module, $controller, $action) = explode('.', $customRoute);
        } else if($module === null){
            $module = F::$config->router['default_module'];
            $action = F::$config->router['default_action'];
            $controller = F::$config->router['default_controller'];
        } else if(!in_array($module, $this->_routeModules)){
            $action = $controller;
            $controller = $module;
            $module = F::$config->router['default_module'];
        }

        $controller = $controller === null ? F::$config->router['default_controller'] : $controller;
        $action = $action === null ? F::$config->router['default_action'] : $action;

        $route = $this->_route($module, $controller, $action);
        if($route){
            if(!$customRoute){
                $this->_setActionParams($route, $validUriForParams);
                if(!$validUriForParams){
                    return null;
                }
            } else if(!$route->getAction()->exists()){
                return null;
            }
            $this->_currentRoute = $route;
        }

        return $this->_currentRoute;
    }

    /**
     * 
     * @param \FPHP\Application\Router\Route
     * @return array|boolean
     */
    private function _setActionParams(Route &$route, &$validUriForParams = null)
    {
        $actionParams = array();
        if(!$route->getAction()->exists()){
            $validUriForParams = false;
            return;
        } else {

            $segmentCount = F::$uri->getSegmentCount();
            $firstSegment = F::$uri->getSegment(1);
            $params = $route->getAction()->getParameters();
            $indexStart = 3;
            if($params){
                if($firstSegment){
                    if(in_array($firstSegment, $this->_routeModules)){
                        $indexStart = 4;
                    }
                } else {
                    $validUriForParams = false;
                    return;
                }
                if(!$params[0]->isOptional() && $segmentCount < $indexStart){
                    $validUriForParams = false;
                    return;
                }

                $i = $indexStart;
                foreach($params as $param){
                    if($i <= $segmentCount){
                        if($segmentValue = F::$uri->getSegment($i++)){
                            $actionParams[] = $segmentValue;
                        }
                    }
                }
                
                $segmentParamsCount = ($segmentCount - $indexStart) + 1;
                $segmentParamsCount = $segmentParamsCount < 0 ? 1 : $segmentParamsCount;
                if($segmentParamsCount > $route->getAction()->getNumberOfParameters()
                    || $segmentParamsCount < $route->getAction()->getNumberOfRequiredParameters()){
                    $validUriForParams = false;
                    return;
                }
            } else {
                if($firstSegment && in_array($firstSegment, $this->_routeModules)){
                    $indexStart = 4;
                }
                if($segmentCount >= $indexStart){
                    $validUriForParams = false;
                    return;
                }
            }
            unset($params, $indexStart, $segmentCount, $firstSegment);
        }
        
        $validUriForParams = true;
        $route->setActionParams($actionParams);
    }

    /**
     * 
     * @return void
     */
    public function clearRoutesList()
    {
        $this->_routes = array();
    }

    /**
     * 
     * @return array
     */
    public function getRoutesList()
    {
        return $this->_routes;
    }
}