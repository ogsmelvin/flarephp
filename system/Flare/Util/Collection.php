<?php

namespace Flare\Util;

use \ArrayObject;

/**
 * 
 * @author anthony
 * 
 */
class Collection extends ArrayObject
{
    /**
     * 
     * @param array $content
     */
    public function __construct(array $content)
    {
        parent::__construct($content);
    }

    /**
     * 
     * @param callable $callback
     * @return void
     */
    public function each($callback)
    {
        if (is_callable($callback)) {
            foreach ($this as $key => &$row) {
                $callback($key, $row);
            }
        } else {
            show_error('Each method parameter must be callable');
        }
    }

    /**
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() ? false : true;
    }

    /**
     * 
     * @return mixed
     */
    public function last()
    {
        return end($this);
    }
    
    /**
     * 
     * @return mixed
     */
    public function first()
    {
        return reset($this);
    }
}