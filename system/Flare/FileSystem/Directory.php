<?php

namespace Flare\FileSystem;

use \UnexpectedValueException;
use Flare\Util\Collection;
use Flare\FileSystem\File;
use \DirectoryIterator;
use \RuntimeException;

/**
 * 
 * @author anthony
 * 
 */
class Directory extends DirectoryIterator
{
    /**
     * 
     * @var boolean
     */
    private $_isValid = true;

    /**
     * 
     * @var boolean
     */
    private $_skipDots = false;

    /**
     * 
     * @param string $path
     * @param int $flags
     */
    public function __construct($path)
    {
        try {
            parent::__construct($path);
        } catch (UnexpectedValueException $ex) {
            $this->_isValid = false;
        } catch (RuntimeException $ex) {
            $this->_isValid = false;
        }
    }

    /**
     * 
     * @param boolean 
     * @return \Flare\FileSystem\Directory
     */
    public function skipDots($switch = true)
    {
        $this->_skipDots = $switch;
        return $this;
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
     * @return \Flare\FileSystem\Directory
     */
    public function current()
    {
        $current = parent::current();
        if ($this->_skipDots && $current->isDot()) {
            $this->next();
            return parent::current();
        }
        return $current;
    }

    /**
     * 
     * @return \Flare\FileSystem\Directory
     */
    public function getParentDirectory()
    {
        $parent = new Directory(dirname($this->getPath()));
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

    /**
     * 
     * @return \Flare\Util\Collection
     */
    public function getFiles()
    {
        $collection = new Collection();
        foreach ($this as $file) {
            if ($file->isFile()) {
                $collection[] = new File($file->getPathname());
            }
        }
        return $collection;
    }

    /**
     * 
     * @return \Flare\Util\Collection
     */
    public function getDirectories()
    {
        $collection = new Collection();
        foreach ($this as $file) {
            if ($file->isDir()) {
                $collection[] = new Directory($file->getPathname());
            }
        }
        return $collection;
    }
}