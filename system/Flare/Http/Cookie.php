<?php

namespace Flare\Http;

/**
 * 
 * @author anthony
 * 
 */
class Cookie
{
    /**
     * 
     * @var \Flare\Http\Cookie
     */
    private static $_instance;

    private function __construct() {}

    /**
     * 
     * @return \Flare\Http\Cookie
     */
    public static function & getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance; 
    }
}