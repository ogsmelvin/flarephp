<?php

namespace Flare\FileSystem;

use \RuntimeException;
use \SplFileObject;

/**
 * 
 * @author anthony
 * 
 */
class File extends SplFileObject
{
    /**
     * 
     * @var boolean
     */
    private $_isValid = true;

    /**
     * 
     * @param string $path
     * @param string $openmode
     * @param boolean $use_include_path
     */
    public function __construct($path, $openmode = 'r', $use_include_path = false)
    {
        try {
            parent::__construct($path, $openmode, $use_include_path);
        } catch (RuntimeException $ex) {
            $this->_isValid = false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function exists()
    {
        return $this->_isValid;
    }
}