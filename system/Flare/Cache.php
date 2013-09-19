<?php

namespace Flare;

use Flare\Flare as F;
use Flare\Registry;

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
     */
    public function __construct(array $params)
    {
        $this->init($params);
    }

    /**
     * 
     * @param array $params
     * @return void
     */
    abstract protected function init(array $params);

    /**
     * 
     * @param array $params
     * @return \Flare\Cache
     */
    public static function instance(array $params = array())
    {
        if (empty(static::$engine)) {
            show_error('Service name must be defined');
        }
        $registry = Registry::get(Registry::CACHE_ENGINES_NAMESPACE);
        if (!$registry->has(static::$engine)) {
            if (!$params) {
                $key = basename(static::$engine);
                if (isset(F::$config->cache_engines[$key])) {
                    $params = F::$config->cache_engines[$key];
                }
            }
            $registry->add(static::$engine, new static($params));
        }
        return $registry->fetch(static::$engine);
    }

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
     * @param int $expiration
     * @return \Flare\Cache
     */
    abstract public function set($key, $value, $expiration = 0);
}