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
    protected $_routes = array();

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
        F::$request->setModule($module)
            ->setController($controller);
        if ($action) {
            F::$request->setAction($action);
        }
        
        $path = F::getApp()->getModulesDirectory()
            .F::$request->getModule()
            .'/'
            .F::getApp()->getControllersDirectory()
            .strtolower(urldecode(F::$request->getController()))
            .'.php';
        if (!file_exists($path)) {
            return null;
        }

        require_once F::getApp()->getModulesDirectory().F::$request->getModule().'/bootstrap.php';
        require_once $path;
        
        $controller = ucwords(F::$request->getModule())."\\Controllers\\".F::$request->getControllerClassName();
        $route = new Route();
        $route->setModule(F::$request->getModule());
        $route->setController(new $controller());
        if (F::$request->getAction()) {
            $route->setAction(new Action($route->getController(), F::$request->getActionMethodName()));
            if ($params) {
                $route->setActionParams($params);
            }
        }
        
        return $route;
    }

    /**
     * 
     * @return string|null
     */
    protected function _getMatchedCustomRoute()
    {
        $uri = trim((string) F::$uri, '/');
        if (isset($this->_routes[$uri])) {
            return $this->_routes[$uri];
        } else {
            foreach ($this->_routes as $key => $class) {
                if (preg_match('#^'.$key.'$#', $uri)) {
                    $class = explode('.', trim($customRoute), 3);
                    if (count($class) < 3) {
                        die("Invalid Custom Route class.");
                    }
                    return $class;
                }
            }
        }
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getConfigModule()
    {
        $customRoute = $this->_getMatchedCustomRoute();
        $module = F::$uri->getSegment(1);

        if ($customRoute) {
            list($module) = $customRoute;
        } elseif (!in_array($module, $this->_routeModules)) {
            $module = F::$config->router['default_module'];
        }
        return $module;
    }

    /**
     * 
     * @param string $class
     * @return \Flare\Application\Router\Route
     */
    public function getErrorRoute($class)
    {
        $module = $controller = $action = null;
        $class = explode('.', $class, 3);
        $count = count($class);
        if ($count >= 3) {
            list($module, $controller, $action) = $class;
        } elseif ($count === 2) {
            $module = F::$config->router['default_module'];
            $controller = $class[0];
            $action = $class[1];
        } elseif ($count === 1) {
            $module = F::$config->router['default_module'];
            $controller = F::$config->router['default_controller'];
            $action = $class[0];
        }
        unset($count, $class);
        return $this->_route($module, $controller, $action);
    }
}