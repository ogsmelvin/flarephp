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
    public function __construct(array $content = array())
    {
        parent::__construct($content);
    }

    /**
     * 
     * @param callable|array|string $callback
     * @return \Flare\Util\Collection
     */
    public function each($callback)
    {
        if (is_callable($callback)) {
            foreach ($this as $key => &$row) {
                $callback($key, $row);
            }
        } elseif (is_array($callback) || is_string($callback)) {
            foreach ($this as $key => &$row) {
                call_user_func_array($callback, array($key, $row));
            }
        } else {
            show_error('Each method parameter must be callable');
        }
        return $this;
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

    /**
     * 
     * @param array|ArrayObject $array
     * @return \Flare\Util\Collection
     */
    public function merge($array)
    {
        if ($array instanceof ArrayObject) {
            $array = $array->getArrayCopy();
        }
        $this->exchangeArray(array_merge($this->getArrayCopy(), $array));
        return $this;
    }
}