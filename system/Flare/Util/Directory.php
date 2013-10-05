<?php

namespace Flare\Util;

use \UnexpectedValueException;
use Flare\Util\Collection;
use \FilesystemIterator;
use Flare\Util\File;

/**
 * 
 * @author anthony
 * 
 */
class Directory extends FilesystemIterator
{
    /**
     * 
     * @var boolean
     */
    private $_isValid = true;

    /**
     * 
     * @param string $path
     * @param int $flags
     */
    public function __construct($path, $flags = null)
    {
        try {
            if ($flags) {
                parent::__construct($path, $flags);
            } else {
                parent::__construct($path);
            }
        } catch (UnexpectedValueException $ex) {
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

    /**
     * 
     * @return \Flare\Util\Directory
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