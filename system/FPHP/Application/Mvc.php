<?php

namespace FPHP\Application;

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
     * @return string
     */
    public function getControllersDirectory()
    {
        return $this->_controllersDirectory;
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
     * @return string
     */
    public function getViewsDirectory()
    {
        return $this->_viewsDirectory;
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
     * @return string
     */
    public function getModulesDirectory()
    {
        return $this->_modulesDirectory;
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
     * @return string
     */
    public function getLayoutsDirectory()
    {
        return $this->_layoutsDirectory;
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
     * @return string
     */
    public function getHelpersDirectory()
    {
        return $this->_helpersDirectory;
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
     * @return string
     */
    public function getAppDirectory()
    {
        return $this->_appDirectory;
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
     * @return string
     */
    public function getSystemDirectory()
    {
        return $this->_sysDirectory;
    }

    /**
     * 
     * @param array $modules
     * @return \FPHP\Application\Mvc
     */
    public function setModules($modules)
    {
        if(!in_array(F::$config->router['default_module'], $modules)){
            $modules[] = F::$config->router['default_module'];
        }
        F::$router->setRoutingModules($modules);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getModules()
    {
        return F::$router->getRoutingModules();
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
     * @return string
     */
    public function getModelsDirectory()
    {
        return $this->_modelsDirectory;
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
     * @return \FPHP\Application\Mvc
     */
    public function preDispatch()
    {
        $route = F::$router->getRoute();
        if(!$route){
            display_error(404);
        }
        $this->_controller = $route->getController();
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
        $view = null;
        if(!F::$router->getRoute()->getActionParams()){
            $view = $this->_controller->{$this->_controller->getAppRequest()->getActionMethodName()}();
        } else {
            $view = call_user_func_array(
                array($this->_controller, $this->_controller->getAppRequest()->getActionMethodName()), 
                F::$router->getRoute()->getActionParams()
            );
        }

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
        $module = $this->_controller->getAppRequest()->getModule();
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
            ->set('request', $this->_controller->getAppRequest())
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