<?php

namespace Flare;

/**
 * 
 * @author anthony
 * 
 */
abstract class Cache
{
    /**
     * 
     * @param array $params
     * @return void
     */
    abstract protected function init(array $params);

    /**
     * 
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \Flare\Cache
     */
    abstract public function set($key, $value);
}