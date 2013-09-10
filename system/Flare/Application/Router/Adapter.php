<?php

namespace Flare\Application\Router;

use Flare\Application\Router\Route\Action;
use Flare\Application\Http\Response;
use Flare\Application\Http\Request;
use Flare\Application\Router\Route;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
abstract class Adapter
{
    /**
     * 
     * @var array
     */
    protected $_routeModules;

    /**
     * 
     * @param array $modules
     * @return \Flare\Application\Router\Adapter
     */
    public function setRoutingModules(array $modules)
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
     * @return \Flare\Application\Router\Route
     */
    abstract public function getRoute();

    /**
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return \Flare\Application\Router\Route
     */
    protected function _route($module, $controller, $action = null, $params = array())
    {
        $request = new Request();
        $request->setModule($module)
            ->setController($controller);
        if ($action) {
            $request->setAction($action);
        }
        
        $path = F::getApp()->getModulesDirectory()
            .$request->getModule()
            .'/'
            .F::getApp()->getControllersDirectory()
            .strtolower(urldecode($request->getController()))
            .'.php';
        if (!file_exists($path)) {
            return null;
        }

        require_once F::getApp()->getModulesDirectory().$request->getModule().'/bootstrap.php';
        require_once $path;
        
        $controller = ucwords($request->getModule())."\\Controllers\\".$request->getControllerClassName();
        $route = new Route();
        $route->setModule($request->getModule());
        $route->setController(new $controller($request, new Response()));
        if ($request->getAction()) {
            $route->setAction(new Action($route->getController(), $request->getActionMethodName()));
            if ($params) {
                $route->setActionParams($params);
            }
        }
        
        return $route;
    }

    /**
     * 
     * @param string $class
     * @return \Flare\Application\Router\Route
     */
    public function getErrorRoute($class)
    {
        $class = explode('.', $class);
        $module = isset($class[0]) ? $class[0] : F::$config->router['default_module'];
        $controller = isset($class[1]) ? $class[1] : F::$config->router['default_controller'];
        $action = isset($class[2]) ? $class[2] : F::$config->router['default_action'];
        return $this->_route($module, $controller, $action);
    }
}