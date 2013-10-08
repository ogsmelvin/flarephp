<?php

namespace Flare\FileSystem;

use \UnexpectedValueException;
use \FileSystemIterator;

/**
 * 
 * @author anthony
 * 
 */
class Directory extends FileSystemIterator
{
    /**
     * 
     * @var boolean
     */
    private $_isValid = true;

    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        try {
            parent::__construct($path, self::CURRENT_AS_SELF);
        } catch (UnexpectedValueException $ex) {
            $this->_isValid = false;
        }
    }

    /**
     * 
     * @return string
     */
    public function getPerms()
    {
        return substr(sprintf('%o', parent::getPerms()), -4);
    }

    /**
     * 
     * @return boolean
     */
    public function exists()
    {
        return $this->_isValid;
    }

    /**
     * 
     * @return \Flare\FileSystem\Directory|null
     */
    public function getParentDirectory()
    {
        $dir = dirname($this->getPath());
        if ($dir == $this->getPath()) {
            return null;
        }
        $parent = new Directory($dir);
        if (!$parent->exists()) {
            return null;
        }
        return $parent;
    }

    /**
     * 
     * @param boolean $switch
     * @return string|int
     */
    public function getSize($convert = false)
    {
        $size = parent::getSize();
        if ($convert) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[$power];
        }
        return $size;
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getMTime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getMTime());
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getCTime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getCTime());
    }

    /**
     * 
     * @param string $dateFormat
     * @return string
     */
    public function getATime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, parent::getATime());
    }
}