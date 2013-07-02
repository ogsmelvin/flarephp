<?php

namespace Flare;

use Flare\Http\Client\Curl;

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
     * @access public
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->curl = new Curl();
        $this->init($params);
    }

    /**
     * 
     * @access protected
     * @param array $param
     * @return void
     */
    abstract protected function init(array $params);
}