<?php

namespace Flare\Application;

use Flare\Objects\Json;

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
    private $_data;

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_data = $data;
    }

    /**
     * 
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_data[$key] = $val;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->_data[$key])) {
            return null;
        }
        return $this->_data[$key];
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * 
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->_data);
    }

    /**
     * 
     * @return \Flare\Objects\Json
     */
    public function toJSONObject()
    {
        return new Json($this->_data);
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
}