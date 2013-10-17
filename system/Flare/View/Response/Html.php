<?php

namespace Flare\View\Response;

use Flare\View\Response;
use Flare\Security\Xss;

/**
 * 
 * @author anthony
 * 
 */
class Html extends Response
{
    /**
     * 
     * @var string
     */
    const EXTENSION_NAME = 'phtml';

    /**
     * 
     * @var string
     */
    private $_includePath;

    /**
     * 
     * @var array
     */
    private $_sections = array();

    /**
     * 
     * @var arrays
     */
    private $_vars = array();

    /**
     * 
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * 
     * @var string
     */
    private $_contentPath;

    /**
     * 
     * @var string
     */
    public $content;

    /**
     * 
     * @var string
     */
    private $_layout;

    /**
     * 
     * @var string
     */
    private $_layoutPath;
    
    /**
     *
     * @var array
     */
    private $_scripts = array();

    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_contentPath = $path.'.'.self::EXTENSION_NAME;
    }

    /**
     * 
     * @param array $data
     * @return \Flare\View\Response\Html
     */
    public function setData(array &$data)
    {
        $this->_vars = & $data;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @param mixed $object
     * @return \Flare\View\Response\Html
     */
    public function with($name, &$object)
    {
        $this->{$name} = & $object;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    private function _compile()
    {
        if (!file_exists($this->_includePath.$this->_contentPath)) {
            show_response(500, "View '{$this->_contentPath}' doesn't exists");
        }

        extract($this->_vars);
        ob_start();
        if ($this->_layout) {
            if (!file_exists($this->_layoutPath.$this->_layout)) {
                show_response(500, "Layout '{$this->_layout}' doesn't exists");
            }
            include $this->_includePath.$this->_contentPath;
            $this->content = (string) ob_get_clean();
            ob_start();
            include $this->_layoutPath.$this->_layout;
        } else {
            include $this->_includePath.$this->_contentPath;
        }
        $html = (string) ob_get_clean();
        
        if ($this->_scripts) {
            $html = preg_replace("#<body(.*)>(.*?)</body>#is", '<body$1>$2'.implode('', $this->_scripts).'</body>', $html);
        }
        
        return $html;
    }

    /**
     * 
     * @return string
     */
    public function render()
    {
        return $this->_compile();
    }

    /**
     * 
     * @param string|boolean $file
     * @return \Flare\View\Response\Html
     */
    public function withLayout($file)
    {
        if ($file === false) {
            $this->_layout = false;
        } else {
            $this->_layout = $file.'_layout.'.self::EXTENSION_NAME;
        }
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function hasLayout()
    {
        return !empty($this->_layout);
    }

    /**
     * 
     * @return boolean
     */
    public function disabledLayout()
    {
        return ($this->_layout === false);
    }

    /**
     * 
     * @param string $content
     * @param boolean $src
     * @return \Flare\View\Response\Html
     */
    public function addScript($content, $src = false)
    {
        if ($src) {
            $this->_scripts[] = "<script type=\"text/javascript\" src=\"{$content}\"></script>";
        } else {
            $this->_scripts[] = '<script type="text/javascript">'.$content.'</script>';
        }
        return $this;
    }

    /**
     * 
     * @param string $path
     * @return \Flare\View\Response\Html
     */
    public function setIncludePath($path)
    {
        $folder = realpath($path);
        $folder = $folder !== false ? rtrim(str_replace("\\", '/', $folder), '/').'/' : rtrim($path, '/').'/';
        $this->_includePath = $folder;
        return $this;
    }

    /**
     * 
     * @param string $path
     * @return \Flare\View\Response\Html
     */
    public function setLayoutPath($path)
    {
        $folder = realpath($path);
        $folder = $folder !== false ? rtrim(str_replace("\\", '/', $folder), '/').'/' : rtrim($path, '/').'/';
        $this->_layoutPath = $folder;
        return $this;
    }

    /**
     * 
     * @param string $path
     * @param boolean $useIncludePath
     * @return string
     */
    public function renderView($path, $useIncludePath = true)
    {
        extract($this->_vars);
        ob_start();
        if ($useIncludePath && $this->_includePath) {
            include $this->_includePath.ltrim($path, '/').'.'.self::EXTENSION_NAME;
        } elseif (!$useIncludePath) {
            include $path.'.'.self::EXTENSION_NAME;
        }
        return (string) ob_get_clean();
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    public function url($url = null)
    {
        if (!$url) {
            $url = rtrim($this->uri->base, '/');
        } elseif (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = $this->uri->base.trim($url, '/');
        }
        return $url;
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    public function moduleUrl($url = null)
    {
        if (!$url) {
            $url = rtrim($this->uri->module, '/');
        } elseif (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = $this->uri->module.trim($url, '/');
        }
        return $url;
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    public function submoduleUrl($url = null)
    {
        if (!$url) {
            $url = rtrim($this->uri->submodule, '/');
        } elseif (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = $this->uri->submodule.trim($url, '/');
        }
        return $url;
    }

    /**
     * 
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return string
     */
    private function _createUrl($module, $controller, $action)
    {
        $params = array();
        if ($action) {
            $params = explode('/', trim($action, '/'));
            $action = array_shift($params);
        } else {
            $action = $this->config->router['default_action'];
        }

        if (!$controller) {
            $controller = $this->request->getController();
        } else {
            $controller = trim($controller, '/');
            if (count(explode('/', $controller)) === 1) {
                $controller = $this->request->getSubmodule().'/'.$controller;
            }
        }
        
        if (!$module) {
            $module = $this->request->getModule();
        } else {
            $module = trim($module, '/');
        }
        
        return $this->uri->create($module.'.'.str_replace('/', '.', $controller).'.'.$action, $params);
    }

    /**
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return string
     */
    public function actionUrl($action, $controller = null, $module = null)
    {
        return $this->_createUrl($module, $controller, $action);
    }

    /**
     * 
     * @param string $controller
     * @param string $action
     * @param string $module
     * @return string
     */
    public function controllerUrl($controller, $action = null, $module = null)
    {
        return $this->_createUrl($module, $controller, $action);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    public function xss($data)
    {
        return Xss::filter($data);
    }

    /**
     * 
     * @param string $name
     * @return void
     */
    public function sectionOpen($name)
    {
        end($this->_sections);
        $lastSection = key($this->_sections);
        if ($lastSection && $this->_sections[$lastSection] === true) {
            show_error("'{$lastSection}' section is currently open");
        } elseif (isset($this->_sections[$name])) {
            show_error("'{$name}' section is already open");
        }
        $this->_sections[$name] = true;
        reset($this->_sections);
        ob_start();
    }

    /**
     * 
     * @return void
     */
    public function sectionClose()
    {
        end($this->_sections);
        $name = key($this->_sections);
        if (!isset($this->_sections[$name])) {
            show_error("'{$name}' is not yet open");
        }
        reset($this->_sections);
        $this->_sections[$name] = (string) ob_get_clean();
    }


    /**
     * 
     * @param string $name
     * @return string
     */
    public function section($name)
    {
        if (!isset($this->_sections[$name])) {
            return null;
        }
        return $this->_sections[$name];
    }
}