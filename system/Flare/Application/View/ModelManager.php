<?php

namespace Flare\Application\View;

/**
 * 
 * @author anthony
 * 
 */
class ModelManager
{
    /**
     * 
     * @var \Flare\Application\View\ModelManager
     */
    private static $_instance = null;

    private function __construct() {}

    /**
     * 
     * @return \Flare\Application\View\ModelManager
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\View\Model
     */
    public function __get($key)
    {
        $key = ucwords($key);
        $class = "\\Flare\\Application\\View\\Model\\{$key}";
    }
}