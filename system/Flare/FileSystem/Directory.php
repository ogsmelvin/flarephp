<?php

namespace Flare\FileSystem;

use \UnexpectedValueException;
use Flare\FileSystem\File;
use \FilesystemIterator;

/**
 * 
 * @author anthony
 * 
 */
class Directory extends FilesystemIterator
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
        }
        try {
            parent::__construct($this->_origPath, self::UNIX_PATHS);
        } catch (UnexpectedValueException $ex) {}
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
     * @return \Flare\FileSystem\File
     */
    public function current()
    {
        return new File(parent::current());
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
    public function getParent()
    {
        if (!$this->hasParent()) {
            return null;
        }
        $dir = new Directory($this->getPath());
        if (!$dir->exists()) {
            return null;
        }

        return $dir;
    }

    /**
     * 
     * @return string
     */
    public function getPath()
    {
        $path = parent::getPath();
        if ($this->_origPath === $path) {
            return str_replace("\\", '/', dirname($this->_origPath));
        }
        return $path;
    }

    /**
     * 
     * @return string
     */
    public function getPathname()
    {
        $pathname = parent::getPathname();
        if ($this->_origPath !== $pathname) {
            return $this->_origPath;
        }
        return $pathname;
    }

    /**
     * 
     * @return string
     */
    public function getFilename()
    {
        $filename = parent::getFilename();
        $origFilename = basename($this->_origPath);
        if ($origFilename !== $filename) {
            return $origFilename;
        }
        return $filename;
    }

    /**
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return (strpos($this->getFilename(), '.') === 0);
    }

    /**
     * 
     * @return boolean
     */
    public function hasParent()
    {
        return (dirname($this->getPath()) !== $this->getPath());
    }
}