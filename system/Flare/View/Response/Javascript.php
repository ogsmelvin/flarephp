<?php

namespace Flare\View\Response;

use Flare\View\Response;

/**
 * 
 * @author anthony
 * 
 */
class Javascript extends Response
{
    /**
     * 
     * @var string
     */
    protected $contentType = 'application/javascript';
    
    /**
     *
     * @var string
     */
    private $_paths = array();
    
    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        $this->merge($path);
    }
    
    /**
     * 
     * @param string $path
     * @return \Flare\View\Response\Javascript
     */
    public function merge($path)
    {
        $this->_paths[] = str_replace('/', DIRECTORY_SEPARATOR, $path);
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    private function _compile()
    {
        $tmp = '';
        $content = '';
        foreach ($this->_paths as $path) {
            ob_start();
            include $path;
            $tmp = (string) ob_get_clean();
            $content .= $tmp ? $tmp."\n" : '';
        }

        unset($tmp);
        return $content;
    }
    
    /**
     * 
     * @return string
     */
    public function render()
    {
        return $this->_compile();
    }
}