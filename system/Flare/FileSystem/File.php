<?php

namespace Flare\FileSystem;

use Flare\FileSystem\FileInfo;
use Flare\FileSystem\Action;

/**
 * 
 * @author anthony
 * 
 */
class File extends FileInfo implements Action
{
    /**
     * 
     * @param string $path
     * @return boolean
     */
    public function move($path)
    {
        if (!$this->exists()) {
            show_error("File '{$this->getPathname()}' doesn't exists");
        }

        return false;
    }

    /**
     * 
     * @param string $path
     * @return boolean
     */
    public function copy($path)
    {
        if (!$this->exists()) {
            show_error("File '{$this->getPathname()}' doesn't exists");
        }
        
        return false;
    }
}