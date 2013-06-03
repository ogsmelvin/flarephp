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
    private static $_instances = array();

    /**
     * 
     * @var string
     */
    private $_name;

    /**
     * 
     * @var string
     */
    private $_tmpname;

    /**
     * 
     * @var string
     */
    private $_filename;

    /**
     * 
     * @var string
     */
    private $_extension;

    /**
     * 
     * @var string
     */
    private $_type;

    /**
     * 
     * @var string
     */
    private $_error;

    /**
     * 
     * @var int
     */
    private $_size;

    /**
     * 
     * @param string $name
     */
    private function __construct($name, $filename, $tmpname, $type, $error, $size)
    {
        $this->_name = $name;
        $this->_filename = $filename;
        $this->_tmpname = $tmpname
        $this->_type = $type;
        $this->_error = $error
        $this->_size = $size;
        $this->_extension = pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * 
     * @param string $name
     * @return \FPHP\Http\File|array
     */
    public static function get($name)
    {
        if(!isset(self::$_instances[$name])){
            if(!isset($_FILES[$name])){
                return null;
            }
            if(is_array($_FILES[$name]['name'])){
                foreach($_FILES[$name]['name'] as $key => $file){
                    self::$_instances[$name][] = new self(
                        $name,
                        $file,
                        $_FILES[$name]['tmp_name'][$key],
                        $_FILES[$name]['type'][$key],
                        $_FILES[$name]['error'][$key],
                        $_FILES[$name]['size'][$key]
                    );
                }
            } else {
                self::$_instances[$name] = new self(
                    $name,
                    $_FILES[$name]['name'],
                    $_FILES[$name]['tmp_name'],
                    $_FILES[$name]['type'],
                    $_FILES[$name]['error'],
                    $_FILES[$name]['size']
                );
            }
        }
        return self::$_instances[$name];
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->_name;
    }

    /**
     * 
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * 
     * @return string
     */
    public function getTempname()
    {
        return $this->_tmpname;
    }

    /**
     * 
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * 
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * 
     * @param string $to
     * @param array $config
     * @return array|false
     */
    public function move($to, $config = array())
    {
        //TODO
    }
}