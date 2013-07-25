<?php

namespace Flare\Service;

use Flare\Service;

/**
 * 
 * @author anthony
 * 
 */
class Twitter extends Service
{
    /**
     * 
     * @var string
     */
    protected static $service = __CLASS__;
    
    /**
     * 
     * @param array $config
     * @return void
     */
    protected function init(array $config)
    {
        
    }

    /**
     * 
     * @param string|array $query
     * @param string|array $options
     * @return \Flare\Service\Twitter\SearchResult
     */
    public function search($query, $options = array())
    {

    }
}