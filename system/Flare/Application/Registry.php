<?php

namespace Flare\Application;

/**
 * 
 * @author anthony
 * 
 */
class Registry
{
    /**
     * 
     * @var string
     */
    const MODELS_NAMESPACE = '__Flare_Models';

    /**
     * 
     * @var string
     */
    const SERVICES_NAMESPACE = '__Flare_Services';

    /**
     * 
     * @var array
     */
    private static $_registry = array();

    /**
     * 
     * @var string 
     */
    private $_namespace;

    /**
     * 
     * @var array
     */
    private $_storage = array();

    /**
     * 
     * @param string $namespace
     */
    private function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * 
     * @param string $namespace
     * @return \Flare\Application\Registry
     */
    public static function getInstance($namespace)
    {
        if (!isset(self::$_registry[$namespace])) {
            self::$_registry[$namespace] = new self($namespace);
        }
        return self::$_registry[$namespace];
    }

    /**
     * 
     * @param string $name
     * @param mixed $object
     * @return \Flare\Application\Registry
     */
    public function add($name, $object)
    {
        $this->_storage[$name] = $object;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \Flare\Service|null
     */
    public function get($name)
    {
        return isset($this->_storage[$name]) ? $this->_storage[$name] : null;
    }

    /**
     * 
     * @param string $name
     * @return \Flare\Application\Registry
     */
    public function remove($name)
    {
        unset($this->_storage[$name]);
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->_storage[$name]);
    }

    /**
     * 
     * @return \Flare\Application\Registry
     */
    public function clear()
    {
        $this->_storage = array();
        return $this;
    }
}