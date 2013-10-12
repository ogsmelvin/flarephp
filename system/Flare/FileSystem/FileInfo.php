<?php

namespace Flare\FileSystem;

use \SplFileInfo;

/**
 * 
 * @author anthony
 * 
 */
abstract class FileInfo extends SplFileInfo
{
    /**
     * 
     * @var string
     */
    private $_origPath;

    /**
     * 
     * @var boolean
     */
    private $_isValid = false;

    /**
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_origPath = realpath($path);
        if ($this->_origPath) {
            $this->_origPath = str_replace("\\", '/', $this->_origPath);
            $this->_isValid = true;
        } else {
            $this->_origPath = $path;
        }
        parent::__construct($this->_origPath);
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
     * @return string
     */
    public function getPerms()
    {
        return substr(sprintf('%o', parent::getPerms()), -4);
    }

    /**
     * 
     * @param boolean $convert
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

    /**
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return (strpos($this->getFilename(), '.') === 0);
    }
}