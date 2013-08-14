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
        $content = '';
        foreach ($this->_paths as $path) {
            $tmp = file_get_contents($path);
            if ($tmp !== false) {
                $content .= $tmp;
            }
            unset($tmp);
        }
        
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