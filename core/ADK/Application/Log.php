<?php

namespace ADK\Application;

/**
 * 
 * @author anthony
 * 
 */
class Log
{
    /**
     * 
     * @var string
     */
    protected $_path;

    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        // if(!is_file($path)){
        //     display_error("{$path} is not a file");
        // }
        $this->_path = $path;
    }

    /**
     * 
     * @param string $text
     * @return int|boolean
     */
    public function write($text)
    {
        $text = trim($text, "\n")."\n";
        return file_put_contents($this->_path, $text, FILE_APPEND);
    }

    /**
     * 
     * @return int|boolean
     */
    public function clear()
    {
        return file_put_contents($this->_path, "");
    }
}