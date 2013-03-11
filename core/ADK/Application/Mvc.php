<?php

namespace ADK\Application;

use ADK\Application\Http\Request;
use ADK\Application\Data;
use ADK\UI\Javascript;
use ADK\UI\Html;
use ADK\Adk as A;
use \Exception;

/**
 * 
 * @author anthony
 * 
 */
class Mvc
{
    /**
     * 
     * @var string
     */
    private $_controllersDirectory = null;

    /**
     * 
     * @var string
     */
    private $_viewsDirectory = null;

    /**
     * 
     * @var string
     */
    private $_modulesDirectory = null;

    /**
     * 
     * @var string
     */
    private $_layoutsDirectory = null;

    /**
     * 
     * @var string
     */
    private $_modelsDirectory = null;

    /**
     * 
     * @var array
     */
    private $_modulesList = array();

    /**
     * 
     * @var boolean
     */
    private $_routes = false;

    /**
     * 
     * @var mixed
     */
    private $_controller = null;

    /**
     * 
     * @var \ADK\Application\Http\Request
     */
    private $_request = null;

    /**
     * 
     * @var boolean
     */
    private $_dispatched = false;

    /**
     * 
     * @param string $directory
     * @return \ADK\Application\Mvc
     */
    public function setControllersDirectory($directory)
    {
        $this->_controllersDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \ADK\Application\Mvc
     */
    public function setViewsDirectory($directory)
    {
        $this->_viewsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \ADK\Application\Mvc
     */
    public function setModulesDirectory($directory)
    {
        $this->_modulesDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \ADK\Application\Mvc
     */
    public function setLayoutsDirectory($directory)
    {
        $this->_layoutsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param array $modules
     * @return \ADK\Application\Mvc
     */
    public function setModules($modules)
    {
        $this->_modulesList = $modules;
        if(!in_array(A::$config->router['default_module'], $this->_modulesList)){
            $this->_modulesList[] = A::$config->router['default_module'];
        }
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \ADK\Application\Mvc
     */
    public function setModelsDirectory($directory)
    {
        if(!$this->_modelsDirectory){
            spl_autoload_register(array($this, 'autoloadModel'));
        }
        $this->_modelsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $class
     * @return void
     */
    public function autoloadModel($class)
    {
        if(strpos($class, 'Models') === 0){
            require $this->_modelsDirectory
                .str_replace(array("Models\\", "\\"), array('', '/'), $class)
                .'.php';
        }
    }

    /**
     * 
     * @param array $routes
     * @return \ADK\Application\Mvc
     */
    public function setRoutes(array $routes)
    {
        if($routes){
            $this->_routes = $routes;
        }
        return $this;
    }

    /**
     * 
     * @return \ADK\Application\Http\Request
     */
    public function getAcceptedRequest()
    {
        return $this->_request;
    }

    /**
     * 
     * @return \ADK\Application\Mvc
     */
    public function preDispatch()
    {
        $module = A::$uri->getSegment(1);
        $controller = A::$uri->getSegment(2);
        $action = A::$uri->getSegment(3);
        if($module === null){
            $module = A::$config->router['default_module'];
            $action = A::$config->router['default_action'];
            $controller = A::$config->router['default_controller'];
        } else if(!in_array($module, $this->_modulesList)){
            $action = $controller;
            $controller = $module;
            $module = A::$config->router['default_module'];
        }

        $controller = $controller === null ? A::$config->router['default_controller'] : $controller;
        $action = $action === null ? A::$config->router['default_action'] : $action;

        $this->_request = new Request();
        $this->_request->setModule($module)
            ->setController($controller)
            ->setAction($action);

        $path = $this->_modulesDirectory
            .$this->_request->getModule()
            .'/'
            .$this->_controllersDirectory
            .$this->_request->getController()
            .'.php';
        if(!file_exists($path)){
            A::$response->setBody("404 page")
                ->setCode(404)
                ->send();
            exit;
        }

        require $this->_modulesDirectory.$this->_request->getModule().'/controller.php';
        require $path;
        $controller = ucwords($this->_request->getModule())."\\Controllers\\".$this->_request->getController();
        $this->_controller = new $controller;
        if(!method_exists($this->_controller, $this->_request->getAction())){
            A::$response->setBody("404 page")
                ->setCode(404)
                ->send();
            exit;
        }
        return $this;
    }

    /**
     * 
     * @return void
     */
    public function dispatch()
    {
        if($this->_dispatched){
            throw new Exception("Already dispatched");
        }

        if(!$this->_controller){
            return;
        }

        $this->_controller->init();
        $view = $this->_controller->{$this->_request->getAction()}($this->_request);
        if($view instanceof Html){
            A::$response->setHeader('Content-Type', 'text/html');
        } else if($view instanceof \ADK\Objects\Xml){
            A::$response->setHeader('Content-Type', 'text/xml');
        } else if($view instanceof \ADK\Objects\Json){
            A::$response->setHeader('Content-Type', 'application/json');
        } else if($view instanceof \ADK\Objects\Image){
            //TODO
        }
        
        A::$response->setBody($view)->send();
        $this->_controller->complete();
        $this->_dispatched = true;
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return \ADK\Objects\Html
     */
    public function view($path, $data = null, $layout = null)
    {
        $module = $this->_request->getModule();
        if($layout === null 
            && isset(A::$config->layout[$module]) 
            && A::$config->layout[$module]['auto'])
        {
            $layout = $this->_layoutsDirectory
                .A::$config->layout[$module]['layout'].'_layout.php';
        } else if($layout !== false && $layout !== null){
            $layout = $this->_layoutsDirectory.$layout.'_layout.php';
        }

        $path = $this->_modulesDirectory
            .$module
            .'/'
            .$this->_viewsDirectory
            .$path
            .'.php';
        if(!file_exists($path)){
            throw new Exception("{$path} not found");
        }
        $html = new Html($path);
        $html->set('uri', A::$uri)
            ->set('request', $this->_request)
            ->set('session', A::$session)
            ->set('js', new Javascript())
            ->set('config', A::$config);
        if($data){
            $html->set('data', new Data($data));
        }
        if($layout){
            $html->setLayout($layout);
        }
        return $html;
    }
}