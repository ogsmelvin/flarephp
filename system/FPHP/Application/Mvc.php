<?php

namespace FPHP\Application;

use FPHP\Application\Http\Request;
use FPHP\Application\Data;
use FPHP\UI\Javascript;
use \ReflectionMethod;
use FPHP\UI\Html;
use FPHP\Fphp as F;
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
     * @var array
     */
    private $_actionParams = array();

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
        if(!in_array(F::$config->router['default_module'], $this->_modulesList)){
            $this->_modulesList[] = F::$config->router['default_module'];
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
        $class = explode("\\", $class);
        if(isset($class[1]) && $class[1] == 'Models'){
            $className = array_pop($class);
            require $this->_modulesDirectory.strtolower(implode("/", $class))."/".$className.'.php';
        }
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
     * @return string
     */
    public function getAppDirectory()
    {
        if(!isset($this->_appDirectory)){
            return null;
        }
        return $this->_appDirectory;
    }

    /**
     * 
     * @return \FPHP\Application\Mvc
     */
    public function preDispatch()
    {
        $route = F::$router->getRoute();
        $module = F::$uri->getSegment(1);
        $controller = F::$uri->getSegment(2);
        $action = F::$uri->getSegment(3);
        if($route){
            list($module, $controller, $action) = explode('.', $route);
        } else if($module === null){
            $module = F::$config->router['default_module'];
            $action = F::$config->router['default_action'];
            $controller = F::$config->router['default_controller'];
        } else if(!in_array($module, $this->_modulesList)){
            $action = $controller;
            $controller = $module;
            $module = F::$config->router['default_module'];
        }

        // if(F::$config->router['url_suffix']){
        //     if($action && F::$uri->getSuffix() !== F::$config->router['url_suffix']){
        //         F::$response->setBody("404 page")
        //             ->setCode(404)
        //             ->send();
        //         exit;
        //     }

        //     $action = rtrim($action, '.'.F::$config->router['url_suffix']);
        // }

        $controller = $controller === null ? F::$config->router['default_controller'] : $controller;
        $action = $action === null ? F::$config->router['default_action'] : $action;

        $this->_request = new Request();
        if(F::$config->auto_xss_filtering){
            $this->_request->setAutoXssFilter(true);
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
            display_error('404 Page', 404);
        }

        require $this->_modulesDirectory.$this->_request->getModule().'/bootstrap.php';
        require $path;
        $controller = ucwords($this->_request->getModule())."\\Controllers\\".$this->_request->getControllerClassName();
        $this->_controller = new $controller($this->_request, F::$response);
        if(!method_exists($this->_controller, $this->_request->getAction())){
            display_error('404 Page', 404);
        } else {
            $method = new ReflectionMethod($this->_controller, $this->_request->getAction());
            if($method->getNumberOfParameters()){
                $segmentCount = F::$uri->getSegmentCount();
                $indexStart = 3;
                if($segmentCount > 3){
                    $indexStart = 4;
                }
                foreach(range($indexStart, $segmentCount) as $index){
                    $this->_actionParams[] = F::$uri->getSegment($index);
                }
                if(count($this->_actionParams) !== $method->getNumberOfParameters()){
                    display_error('404 Page', 404);
                }
            }
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

        if(!F::$response->hasContentType()){
            if($view instanceof Html){
                F::$response->setContentType('text/html');
            } else if($view instanceof \FPHP\Objects\Xml){
                $view = $view->asXml();
                F::$response->setContentType('text/xml');
            } else if($view instanceof \FPHP\Objects\Json){
                F::$response->setContentType('application/json');
            } else if($view instanceof \FPHP\Objects\Image){
                //TODO
            }
        }
        
        $this->_controller->postDispatch();
        F::$response->setBody($view)->send();
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
            && isset(F::$config->layout[$module]) 
            && F::$config->layout[$module]['auto'])
        {
            $layout = $this->_layoutsDirectory
                .F::$config->layout[$module]['layout'].'_layout.php';
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
        $html->set('uri', F::$uri)
            ->set('request', $this->_request)
            ->set('session', F::$session)
            ->set('js', new Javascript())
            ->set('config', F::$config);
        if($data === null){
            $data = new Data();
        } else if(!($data instanceof Data) && is_array($data)){
            $data = new Data($data);
        }
        $html->set('data', $data);
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
     * @return \FPHP\Application\AbstractController
     */
    public function getController()
    {
        return $this->_controller;
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
        F::init(require $this->_appDirectory.'config/config.php');
        $this->setModules(F::$config->modules)
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