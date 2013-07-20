<?php

namespace Flare\View\Response;

use Flare\View\Response;
use Flare\View;

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
    protected $contentType = 'text/html';
    
    /**
     * 
     * @var array
     */
    private $_data;

    /**
     * 
     * @var string
     */
    private $_contentPath;

    /**
     * 
     * @var string
     */
    private $_layoutPath;

    /**
     * 
     * @var \Flare\UI\Javascript
     */
    public $js;

    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_contentPath = $path;
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \Flare\UI\Html
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function render()
    {
        $uri = null;
        $request = null;
        $data = null;
        $session = null;
        $config = null;
        $view = new View();

        foreach ($this->_data as $key => $value) {
            ${$key} = $value;
        }

        ob_start();
        include $this->_contentPath;
        $view->setContent((string) ob_get_clean());

        if (isset($this->_layoutPath)) {
            ob_start();
            include $this->_layoutPath;
            $view->setContent((string) ob_get_clean());
        }

        return $view->getContent();
    }

    /**
     * 
     * @param string $file
     * @return \Flare\UI\Html
     */
    public function setLayout($file)
    {
        $this->_layoutPath = $file;
        return $this;
    }
}