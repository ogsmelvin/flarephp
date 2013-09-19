<?php

namespace Flare\View\Response;

use Flare\View\Response;

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
        if (!file_exists($path.'.'.self::EXTENSION_NAME)) {
            show_response(500, "{$path} not found");
        }
        $this->_contentPath = $path.'.'.self::EXTENSION_NAME;
    }

    /**
     * 
     * @param array $data
     * @return \Flare\View\Response\Html
     */
    public function setData(array $data)
    {
        $this->_vars = $data;
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
        extract($this->_vars);
        ob_start();
        if ($this->_layoutPath) {
            include $this->_contentPath;
            $this->content = (string) ob_get_clean();
            ob_start();
            include $this->_layoutPath;
        } else {
            include $this->_contentPath;
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
     * @param string $file
     * @return \Flare\View\Response\Html
     */
    public function setLayout($file)
    {
        $this->_layoutPath = $file.'.'.self::EXTENSION_NAME;
        return $this;
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
     * @return \Flare\View
     */
    public function setIncludePath($path)
    {
        $folder = realpath($path);
        $folder = $folder !== false ? rtrim(str_replace("\\", '/', $folder), '/').'/' : rtrim($path, '/').'/';
        $this->_includePath = $folder;
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