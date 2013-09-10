<?php

namespace Flare;

use Flare\Application\ErrorController;
use Flare\Application\Dispatcher;
use Flare\View\Response\Html;
use Flare\Application\Config;
use Flare\Application\Router;
use Flare\Util\Collection;
use Flare\Application\Db;
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
     * @var \Flare\Application\AbstractController
     */
    private $_controller;

    /**
     * 
     * @var boolean
     */
    private $_dispatched = false;

    /**
     * 
     * @var boolean
     */
    private $_configured = false;
    
    /**
     *
     * @var boolean
     */
    private $_predispatched = false;

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
                $this->error(500, 'No route found. Predispatch must be executed first.');
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
                $this->error(500, 'No route found. Predispatch must be executed first.');
            }
            $module = F::$router->getRoute()->getModule();
        }

        $path = $this->_modulesDirectory
            .$module
            .'/config/';
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
    private function _predispatch()
    {
        if (F::$uri->isValid()) {
            $route = F::$router->getRoute();
            if (!$route) {
                $this->error(404);
            } elseif ($route->getController() instanceof ErrorController
                && $route->getController()->response->getCode() === Response::DEFAULT_CODE)
            {
                $route->getController()->response->setCode(404);
            }
        } else {
            $this->error(400);
        }
        
        $this->_controller = F::$router->getRoute()->getController();
        if (F::$router->getAdapterName() == Router::DEFAULT_ADAPTER) {
            View::create()->setIncludePath($this->getModuleViewsDirectory());
        }
        
        F::$uri->setModuleUrl();
        $this->_predispatched = true;
        return $this;
    }

    /**
     * 
     * @return void
     */
    private function _dispatch()
    {
        if (!$this->_controller) {
            $this->error(500, 'Controller is not initilized');
        }

        if (!$this->_configured) {
            $this->_configure();
            $this->_configured = true;
        }
        
        $dispatcher = new Dispatcher($this->_controller, F::$router->getAdapterName());
        if ($dispatcher->dispatch()) {
            $this->_dispatched = true;
        }

        return $this;
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return \Flare\View\Response\Html
     */
    public function view($path, $data = null, $layout = null)
    {
        $module = $this->_controller->request->getModule();
        if ($layout === null 
            && isset(F::$config->layout[$module]) 
            && F::$config->layout[$module]['auto'])
        {
            $layout = $this->_layoutsDirectory
                .F::$config->layout[$module]['layout'].'_layout.'.View::EXTENSION_NAME;
        } elseif ($layout !== false && $layout !== null) {
            $layout = $this->_layoutsDirectory.$layout.'_layout.'.View::EXTENSION_NAME;
        }

        $path = $this->getModuleViewsDirectory().$path.'.'.View::EXTENSION_NAME;
        if (!file_exists($path)) {
            $this->error(500, "{$path} not found");
        }
        $html = new Html($path);
        $html->set('uri', F::$uri)
            ->set('request', $this->_controller->request)
            ->set('session', F::$session)
            ->set('config', F::$config);
        if ($data === null) {
            $data = new Collection(array(), Collection::ARRAY_AS_PROPS);
        } elseif (!($data instanceof Collection) && is_array($data)) {
            $data = new Collection($data, Collection::ARRAY_AS_PROPS);
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
     * @param boolean $skipConfig
     * @return void
     */
    public function error($code, $message = '', $skipConfig = false)
    {
        $html = null;
        F::$router->setAdapter('page');
        if (!$skipConfig && !empty(F::$config->router['errors'])) {

            $route = null;
            if (is_array(F::$config->router['errors'])) {
                if (isset(F::$config->router['errors'][$code])) {
                    $route = F::$router->useErrorRoute(F::$config->router['errors'][$code]);
                }
            } else {
                $route = F::$router->useErrorRoute(F::$config->router['errors']);
            }
            if (!$route || !($route->getController() instanceof ErrorController)) {
                $this->error($code, $message, true);
            }

            $route->getController()->setErrorCode($code);
            if ($message) {
                $route->getController()->setErrorMessage($message);
            }
            if ($this->_predispatched) {
                $this->_predispatch();
                if (!$this->_dispatched) {
                    $this->_dispatch()
                        ->shutdown(true);
                }
            }
            return;
            
        } elseif ($message) {
            $html = '<pre>'.$message.'</pre>';
        } elseif (isset(Response::$messages[$code])) {
            $html = '<pre>'.Response::$messages[$code].'</pre>';
        }
        
        F::$response->setCode($code)
            ->setBody($html)
            ->send();
        $this->shutdown(true);
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
            $this->error(500, 'Application is already started');
        } elseif (!$this->_appDirectory) {
            $this->error(500, 'App Directory and System Directory must be set');
        }
        $this->setConfigDirectory($this->_appDirectory.'config')
            ->setModulesDirectory($this->_appDirectory.'modules')
            ->setHelpersDirectory($this->_appDirectory.'helpers')
            ->setLayoutsDirectory($this->_appDirectory.'layouts')
            ->setControllersDirectory('controllers')
            ->setModelsDirectory('models')
            ->setViewsDirectory('views')
            ->setLibrariesDirectory($this->_appDirectory.'libraries')
            ->_init()
            ->setModules(F::$config->modules)
            ->_predispatch()
            ->_dispatch()
            ->shutdown();
    }

    /**
     * 
     * @param boolean $withExit
     * @return void
     */
    public function shutdown($withExit = false)
    {
        Db::disconnect();
        if ($withExit) {
            exit;
        }
    }

    /**
     *
     * @return \Flare\Application
     */
    private function _init()
    {
        F::$config = Config::load(require $this->_appDirectory.'config/config.php');
        F::$request = new Request();
        F::$response = new Response();
        F::$uri = new Uri();
        F::$router = new Router();
        if (F::$uri->getSegmentCount() == 1
            && pathinfo(F::$uri->getSegment(1), PATHINFO_EXTENSION) == 'js')
        {
            F::$router->setAdapter('javascript');
        } elseif (F::$request->isAjax()
            && F::$request->isPost()
            && F::$uri->getSegmentCount() == 2
            && pathinfo(F::$uri->getSegment(2), PATHINFO_EXTENSION) == 'do')
        {
            F::$router->setAdapter('model');
        }
        return $this;
    }

    /**
     * 
     * @return void
     */
    private function _setupCookie()
    {
        $conf = F::$config->cookie;
        if (!empty($conf['namespace'])) {
            if ($conf['enable_encryption'] && !$conf['encryption_key']) {
                $this->error(500, 'Config[encryption_key] must be set');
            }
            F::$cookie = Cookie::create(
                $conf['namespace'],
                $conf['expiration'],
                $conf['enable_encryption'] ? $conf['encryption_key'] : false
            );
        } else {
            $this->error(500, 'Config[cookie][namespace] must be set');
        }
    }

    /**
     * 
     * @return void
     */
    private function _setupRouter()
    {
        $router = F::$config->router;
        if (isset($router['routes']) && F::$router->getAdapterName() == Router::DEFAULT_ADAPTER) {
            F::$router->getAdapter()->addRoutes($router['routes']);
        }
        if (!empty($router['require_https'])) {
            F::$router->secure();
        }
    }

    /**
     * 
     * @return void
     */
    private function _setupSession()
    {
        $session = F::$config->session;
        if (!empty($session['namespace'])) {
            F::$session = Session::create(
                $session['namespace'],
                $session['auto_start']
            );
        } else {
            $this->error(500, 'Config[session][namespace] must be set');
        }
    }

    /**
     * 
     * @param string $module
     * @param array $components
     * @return \Flare\Application
     */
    private function _configure($module = null, $components = array())
    {
        if (!$module) {
            $module = $this->getModuleConfigDirectory();
        } else {
            $module = $module.'/config/';
        }
        
        if (file_exists($module.'config.php')) {
            $moduleConfig = require $module.'config.php';
            if (!is_array($moduleConfig)) {
                $this->error(500, 'Module config.php must return an array.');
            }
            unset($moduleConfig['modules']);
            F::$config->merge($moduleConfig);
            unset($moduleConfig);
        }
        
        if (F::$config->time_limit !== null) {
            set_time_limit(F::$config->time_limit);
        }
        if (F::$config->memory_limit !== null) {
            ini_set('memory_limit', F::$config->memory_limit);
        }
        if (F::$config->timezone !== null) {
            date_default_timezone_set(F::$config->timezone);
        }
        
        if (!$components) {
            $this->_setupRouter();
            $this->_setupSession();
            $this->_setupCookie();
        } else {
            foreach ($components as $component) {
                $this->{'_setup'.$component}();
            }
        }
        
        $this->_compress();
        return $this;
    }
    
    /**
     * 
     * @return void
     */
    private function _compress()
    {
        if (F::$config->auto_compress && !@ini_get('zlib.output_compression')
            && extension_loaded('zlib') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
            && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
        {
            if (!ob_start('ob_gzhandler')) {
                $this->error(500, 'output compression failed');
            }
        }
    }
}