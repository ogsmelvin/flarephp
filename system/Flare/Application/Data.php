<?php

namespace Flare\Application;

/**
 * 
 * @author anthony
 * 
 */
class Data
{
    /**
     * 
     * @var array
     */
    private $data = array();

    /**
     * 
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function __set($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * 
     * @param string $key
     * @param array $data
     * @return \Flare\Application\Data
     */
    public function merge($key, $data)
    {
        $this->data[$key] = array_merge($this->data[$key], (array) $data);
        return $this;
    }
}