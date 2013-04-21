<?php

namespace FPHP\Application;

use FPHP\Application\Http\Request;
use FPHP\Application\Data;
use FPHP\UI\Javascript;
use FPHP\UI\Html;
use FPHP\Fphp as A;
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
     * @var string
     */
    private $_helpersDirectory = null;

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
     * @var string
     */
    private $_appDirectory = null;

    /**
     * 
     * @var string
     */
    private $_sysDirectory = null;

    /**
     * 
     * @var mixed
     */
    private $_controller = null;

    /**
     * 
     * @var \FPHP\Application\Http\Request
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
     * @return \FPHP\Application\Mvc
     */
    public function setControllersDirectory($directory)
    {
        $this->_controllersDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setViewsDirectory($directory)
    {
        $this->_viewsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setModulesDirectory($directory)
    {
        $this->_modulesDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setLayoutsDirectory($directory)
    {
        $this->_layoutsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setHelpersDirectory($directory)
    {
        $this->_helpersDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setAppDirectory($directory)
    {
        $this->_appDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $directory
     * @return \FPHP\Application\Mvc
     */
    public function setSystemDirectory($directory)
    {
        $this->_sysDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param array $modules
     * @return \FPHP\Application\Mvc
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
     * @return \FPHP\Application\Mvc
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
        $class = explode("\\", strtolower($class));
        if(isset($class[1]) && $class[1] == 'models'){
            $className = array_pop($class);
            require $this->_modulesDirectory.implode("/", $class)."/".ucwords($className).'.php';
        }
    }

    /**
     * 
     * @param array $routes
     * @return \FPHP\Application\Mvc
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
     * @return \FPHP\Application\Http\Request
     */
    public function getAcceptedRequest()
    {
        return $this->_request;
    }

    /**
     * 
     * @return \FPHP\Application\Mvc
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
        if(A::$config->auto_xss_filtering){
            $this->_request->setAutoFilter(true);
        }
        $this->_request->setModule($module)
            ->setController($controller)
            ->setAction($action);

        $path = $this->_modulesDirectory
            .$this->_request->getModule()
            .'/'
            .$this->_controllersDirectory
            .strtolower(urldecode($controller))
            .'.php';
        if(!file_exists($path)){
            A::$response->setBody("404 page")
                ->setCode(404)
                ->send();
            exit;
        }

        require $this->_modulesDirectory.$this->_request->getModule().'/bootstrap.php';
        require $path;
        $controller = ucwords($this->_request->getModule())."\\Controllers\\".$this->_request->getController();
        $this->_controller = new $controller($this->_request, A::$response);
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
        $this->_controller->preDispatch();
        $view = $this->_controller->{$this->_request->getAction()}();

        if(!A::$response->getContentType()){
            if($view instanceof Html){
                A::$response->setContentType('text/html');
            } else if($view instanceof \FPHP\Objects\Xml){
                $view = $view->asXml();
                A::$response->setContentType('text/xml');
            } else if($view instanceof \FPHP\Objects\Json){
                A::$response->setContentType('application/json');
            } else if($view instanceof \FPHP\Objects\Image){
                //TODO
            }
        }
        
        $this->_controller->postDispatch();
        A::$response->setBody($view)->send();
        $this->_controller->complete();
        $this->_dispatched = true;
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return \FPHP\Objects\Html
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

    /**
     *
     * @param string $helper
     * @return void
     */
    public function helper($helper)
    {
        $helper = ucwords(strtolower($helper));
        if(file_exists(FPHP_DIR.'FPHP/Helpers/'.$helper.'.php')){
            require_once FPHP_DIR.'FPHP/Helpers/'.$helper.'.php';
        } else if(file_exists($this->_helpersDirectory.$helper.'.php')){
            require_once $this->_helpersDirectory.$helper.'.php';
        }
    }

    /**
     * 
     * @return void
     */
    public function start()
    {
        if($this->_dispatched){
            throw new Exception("Application is already started");
        } else if(!$this->_appDirectory){
            throw new Exception("App Directory and System Directory must be set");
        }
        A::init(require $this->_appDirectory.'config/config.php');
        $this->setModules(A::$config->modules)
            ->setModulesDirectory($this->_appDirectory.'modules')
            ->setModelsDirectory('models')
            ->setHelpersDirectory($this->_appDirectory.'helpers')
            ->setLayoutsDirectory($this->_appDirectory.'layouts')
            ->setControllersDirectory('controllers')
            ->setViewsDirectory('views')
            ->preDispatch()
            ->dispatch();
    }
}