<?php

namespace Flare\FileSystem;

/**
 * 
 * @author anthony
 * 
 */
interface Action
{
    /**
     * 
     * @param string $path
     * @return boolean
     */
    public function move($path);

    /**
     * 
     * @param string $path
     * @return boolean
     */
    public function copy($path);
}