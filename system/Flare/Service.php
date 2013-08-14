<?php

namespace Flare;

use Flare\Http\Client\Curl;
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
     * @var \Flare\Http\Client\Curl
     */
    protected $curl;

    /**
     * 
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->curl = new Curl();
        $this->init($params);
    }

    /**
     * 
     * @param array $params
     * @return \Flare\Service
     */
    public static function i(array $params = array())
    {
        if (empty(static::$service)) {
            show_error('Service name must be defined');
        }
        $registry = Registry::get(Registry::SERVICES_NAMESPACE);
        if (!$registry->has(static::$service)) {
            if (!$params) {
                $key = basename(static::$service);
                if (!isset(F::$config->services[$key])) {
                    show_error("Service '{$key}' config is not defined");
                }
                $params = F::$config->services[$key];
            }
            $registry->add(static::$service, new static($params));
        }
        return $registry->fetch(static::$service);
    }

    /**
     * 
     * @access protected
     * @param array $param
     * @return void
     */
    abstract protected function init(array $params);
}