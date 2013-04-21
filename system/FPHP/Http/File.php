<?php

namespace FPHP\Http;

use \Exception;

/**
 * 
 * @author anthony
 * 
 */
class File
{
    /**
     * 
     * @var array
     */
    private static $_instances;

    /**
     * 
     * @var string
     */
    private $_name;

    /**
     * 
     * @param string $name
     */
    private function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * 
     * @param string $name
     * @return \FPHP\Http\File
     */
    public static function get($name)
    {
        if(!isset($_FILES[$name])){
            return null;
        }
        if(!isset(self::$_instances[$name])){
            self::$_instances[$name] = new self($name);
        }
        return self::$_instances[$name];
    }

    /**
     * 
     * @return boolean
     */
    public function exists()
    {
        return !empty($_FILES[$this->_name]['name']);
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        if(!isset($_FILES[$this->_name]['name'])){
            return null;
        }
        return $_FILES[$this->_name]['name'];
    }

    /**
     * 
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * 
     * @return string
     */
    public function getType()
    {
        if(!isset($_FILES[$this->_name]['type'])){
            return null;
        }
        return $_FILES[$this->_name]['type'];
    }

    /**
     * 
     * @return string
     */
    public function getTempname()
    {
        if(!isset($_FILES[$this->_name]['tmp_name'])){
            return null;
        }
        return $_FILES[$this->_name]['tmp_name'];
    }

    /**
     * 
     * @return int
     */
    public function getSize()
    {
        if(!isset($_FILES[$this->_name]['size'])){
            return 0;
        }
        return (int) $_FILES[$this->_name]['size'];
    }

    /**
     * 
     * @return array
     */
    public function getError()
    {
        if(!isset($_FILES[$this->_name]['error'])){
            return null;
        }
        return $_FILES[$this->_name]['error'];
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}