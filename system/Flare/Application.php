<?php

namespace Flare;

use Flare\View\Response as ViewResponse;
use Flare\View\Response\Html;
use Flare\Application\Config;
use Flare\Application\Router;
use Flare\Application\Data;
use Flare\Http\Response;
use Flare\Http\Request;
use Flare\Http\Session;
use Flare\Http\Cookie;
use Flare\Flare as F;
use Flare\Http\Uri;
use Flare\View;

/**
 * 
 * @author anthony
 * 
 */
class Application
{
    /**
     * 
     * @var string
     */
    private $_controllersDirectory;

    /**
     * 
     * @var string
     */
    private $_errorLayoutsDirectory;

    /**
     * 
     * @var string
     */
    private $_viewsDirectory;

    /**
     * 
     * @var string
     */
    private $_modulesDirectory;

    /**
     * 
     * @var string
     */
    private $_layoutsDirectory;

    /**
     * 
     * @var string
     */
    private $_modelsDirectory;

    /**
     * 
     * @var string
     */
    private $_librariesDirectory;

    /**
     * 
     * @var string
     */
    private $_helpersDirectory;

    /**
     * 
     * @var string
     */
    private $_configDirectory;

    /**
     * 
     * @var string
     */
    private $_appDirectory;

    /**
     * 
     * @var string
     */
    private $_sysDirectory;

    /**
     * 
     * @var mixed
     */
    private $_controller;

    /**
     * 
     * @var boolean
     */
    private $_dispatched = false;

    /**
     * 
     * @param string $directory
     * @return \Flare\Application
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
     * @return \Flare\Application
     */
    public function setErrorsDirectory($directory)
    {
        $this->_errorLayoutsDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getErrorsDirectory()
    {
        return $this->_errorLayoutsDirectory;
    }

    /**
     * 
     * @param string $directory
     * @return \Flare\Application
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
     * @param string $module
     * @return string
     */
    public function getModuleViewsDirectory($module = null)
    {
        if (!$module) {
            if (!F::$router->getRoute()) {
                show_error('No route found. Predispatch must be executed first.');
            }
            $module = F::$router->getRoute()->getModule();
        }

        $path = $this->_modulesDirectory
            .$module
            .'/'
            .$this->_viewsDirectory;
        return $path;
    }

    /**
     * 
     * @param string $module
     * @return string
     */
    public function getModuleConfigDirectory($module = null)
    {
        if (!$module) {
            if (!F::$router->getRoute()) {
                show_error('No route found. Predispatch must be executed first.');
            }
            $module = F::$router->getRoute()->getModule();
        }

        $path = $this->_modulesDirectory
            .$module
            .'/'
            .$this->_configDirectory;
        return $path;
    }

    /**
     * 
     * @param string $directory
     * @return \Flare\Application
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
     * @return \Flare\Application
     */
    public function setConfigDirectory($directory)
    {
        $this->_configDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getConfigDirectory()
    {
        return $this->_configDirectory;
    }

    /**
     * 
     * @param string $directory
     * @return \Flare\Application
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
     * @return \Flare\Application
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
     * @return \Flare\Application
     */
    public function setLibrariesDirectory($directory)
    {
        if (!$this->_librariesDirectory) {
            spl_autoload_register(array($this, 'autoloadLibrary'));
        }
        $this->_librariesDirectory = rtrim(str_replace("\\", '/', $directory), '/').'/';
        return $this;
    }

    /**
     * 
     * @param string $class
     * @return void
     */
    public function autoloadLibrary($class)
    {
        require $this->_librariesDirectory.str_replace("\\", "/", ltrim($class, "\\")).'.php';
    }

    /**
     * 
     * @return string
     */
    public function getLibrariesDirectory()
    {
        return $this->_librariesDirectory;
    }

    /**
     * 
     * @param string $directory
     * @return \Flare\Application
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
     * @return \Flare\Application
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
     * @return \Flare\Application
     */
    public function setModules($modules)
    {
        if (!in_array(F::$config->router['default_module'], $modules)) {
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
     * @return \Flare\Application
     */
    public function setModelsDirectory($directory)
    {
        if (!$this->_modelsDirectory) {
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
        if (isset($class[1]) && $class[1] == 'Models') {
            $className = array_pop($class);
            require $this->_modulesDirectory.strtolower(implode("/", $class))."/".$className.'.php';
        }
    }

    /**
     * 
     * @return \Flare\Application
     */
    public function predispatch()
    {
        $route = F::$router->getRoute();
        if (!$route) {
            show_response(404);
        }
        
        $this->_controller = $route->getController();
        View::create()->setIncludePath($this->getModuleViewsDirectory());
        F::$uri->setModuleUrl();
        return $this;
    }

    /**
     * 
     * @return void
     */
    public function dispatch()
    {
        if ($this->_dispatched) {
            show_error('Already dispatched');
        }

        if (!$this->_controller) {
            show_error('Controller is not initilized');
        }

        $this->_controller->init();
        $this->_controller->predispatch();
        $view = null;
        if (!$this->_controller->router->getRoute()->getActionParams()) {
            $view = $this->_controller->{$this->_controller->request->getActionMethodName()}();
        } else {
            $view = call_user_func_array(
                array($this->_controller, $this->_controller->request->getActionMethodName()), 
                $this->_controller->router->getRoute()->getActionParams()
            );
        }

        if (!$this->_controller->response->hasContentType()) {
            if (!($view instanceof ViewResponse)) {
                if (!empty($view)) {
                    show_error("Action must return a 'View\Response' instance");
                } elseif (F::$config->default_content_type) {
                    $this->_controller->response->setContentType(F::$config->default_content_type);
                }
            } else {
                $this->_controller->response->setContentType($view->getContentType());
            }
        }
        
        $this->_controller->postdispatch();
        if ($this->_controller->cookie->hasNewData()) {
            $this->_controller->response->addCookie(
                $this->_controller->cookie->getNamespace(),
                $this->_controller->cookie->serialize(),
                $this->_controller->cookie->getExpiration()
            );
        }
        $this->_controller->response->setBody($view)->send();
        $this->_controller->complete();
        $this->_dispatched = true;
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return \Flare\Object\Html
     */
    public function view($path, $data = null, $layout = null)
    {
        $module = $this->_controller->request->getModule();
        if ($layout === null 
            && isset(F::$config->layout[$module]) 
            && F::$config->layout[$module]['auto'])
        {
            $layout = $this->_layoutsDirectory
                .F::$config->layout[$module]['layout'].'_layout.php';
        } elseif ($layout !== false && $layout !== null) {
            $layout = $this->_layoutsDirectory.$layout.'_layout.php';
        }

        $path = $this->getModuleViewsDirectory().$path.'.php';
        if (!file_exists($path)) {
            show_error("{$path} not found");
        }
        $html = new Html($path);
        $html->set('uri', F::$uri)
            ->set('request', $this->_controller->request)
            ->set('session', F::$session)
            ->set('config', F::$config);
        if ($data === null) {
            $data = new Data();
        } elseif (!($data instanceof Data) && is_array($data)) {
            $data = new Data($data);
        }
        $html->set('data', $data);
        if ($layout) {
            $html->setLayout($layout);
        }
        return $html;
    }

    /**
     * 
     * @param int $code
     * @param string $message
     * @return void
     */
    public function error($code, $message = '')
    {
        $html = null;
        if (isset(F::$config->router['errors'][$code]) 
            && file_exists($this->_errorLayoutsDirectory.$code.'.php'))
        {
            $html = new Html($this->_errorLayoutsDirectory.$code.'.php');
            $html->set('message', $message);
        } elseif ($message) {
            $html = '<pre>'.$message.'</pre>';
        } elseif (isset(Response::$messages[$code])) {
            $html = '<pre>'.Response::$messages[$code].'</pre>';
        }
        F::$response->setCode($code)
            ->setBody($html)
            ->send();
        exit;
    }

    /**
     *
     * @param string $helper
     * @return void
     */
    public function helper($helper)
    {
        $helper = ucwords(strtolower($helper));
        if (file_exists(FLARE_DIR.'Flare/Helper/'.$helper.'.php')) {
            require_once FLARE_DIR.'Flare/Helper/'.$helper.'.php';
        } elseif (file_exists($this->_helpersDirectory.$helper.'.php')) {
            require_once $this->_helpersDirectory.$helper.'.php';
        }
    }

    /**
     * 
     * @return \Flare\Application\AbstractController
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
        if ($this->_dispatched) {
            show_error("Application is already started");
        } elseif (!$this->_appDirectory) {
            show_error("App Directory and System Directory must be set");
        }
        $this->init()
            ->setModules(F::$config->modules)
            ->setConfigDirectory($this->_appDirectory.'config')
            ->setModulesDirectory($this->_appDirectory.'modules')
            ->setHelpersDirectory($this->_appDirectory.'helpers')
            ->setLayoutsDirectory($this->_appDirectory.'layouts')
            ->setErrorsDirectory($this->_appDirectory.'errors')
            ->setControllersDirectory('controllers')
            ->setModelsDirectory('models')
            ->setViewsDirectory('views')
            ->setLibrariesDirectory($this->_appDirectory.'libraries')
            ->predispatch()
            ->configure()
            ->dispatch();
    }

    /**
     *
     * @return \Flare\Application
     */
    private function init()
    {
        F::$config = Config::load(require $this->_appDirectory.'config/config.php');
        F::$request = new Request();
        F::$response = new Response();
        F::$uri = new Uri();
        F::$router = new Router();
        return $this;
    }

    /**
     * 
     * @return \Flare\Application
     */
    private function configure()
    {
        if (F::$config->time_limit !== null) {
            set_time_limit(F::$config->time_limit);
        }
        if (F::$config->memory_limit !== null) {
            ini_set('memory_limit', F::$config->memory_limit);
        }
        if (F::$config->timezone !== null) {
            date_default_timezone_set(F::$config->timezone);
        }

        if (F::$config->router['routes']) {
            F::$router->addRoutes(F::$config->router['routes']);
        }

        if (F::$config->session['namespace']) {
            F::$session = Session::create(
                F::$config->session['namespace'],
                F::$config->session['auto_start']
            );
        } else {
            show_error('Config[session][namespace] must be set');
        }

        if (F::$config->cookie['namespace']) {
            if (F::$config->cookie['enable_encryption'] && !F::$config->cookie['encryption_key']) {
                show_error('Config[encryption_key] must be set');
            }
            F::$cookie = Cookie::create(
                F::$config->cookie['namespace'],
                F::$config->cookie['expiration'],
                F::$config->cookie['enable_encryption'] ? F::$config->cookie['encryption_key'] : false
            );
        } else {
            show_error('Config[cookie][namespace] must be set');
        }

        if (F::$config->router['require_https']) {
            F::$router->secure();
        }
        if (F::$config->auto_compress && !@ini_get('zlib.output_compression')
            && extension_loaded('zlib') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
            && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
        {
            if (!ob_start('ob_gzhandler')) {
                show_response(500, 'output compression failed');
            }
        }

        return $this;
    }
}