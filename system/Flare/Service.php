<?php

namespace Flare;

use Flare\Flare as F;
use Flare\Registry;

/**
 * 
 * @author anthony
 * 
 */
abstract class Service
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
     * @return \Flare\Service
     */
    public static function instance(array $params = array())
    {
        if (empty(static::$service)) {
            show_error('Service name must be defined');
        }
        $registry = Registry::get(Registry::SERVICES_NAMESPACE);
        if (!$registry->has(static::$service)) {
            if (!$params) {
                $key = basename(str_replace("\\", '/', static::$service));
                if (isset(F::$config->services[$key])) {
                    $params = F::$config->services[$key];
                }
            }
            $registry->add(static::$service, new static($params));
        }
        return $registry->fetch(static::$service);
    }

    /**
     * 
     * @param array $param
     * @return void
     */
    abstract protected function init(array $params);
}