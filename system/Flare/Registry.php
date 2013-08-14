<?php

namespace Flare;

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
     * @var string
     */
    const CACHE_ENGINES_NAMESPACE = '__Flare_Cache';
    
    /**
     * 
     * @var string
     */
    const EVENTS_NAMESPACE = '__Flare_Events';

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
    private function __construct($namespace, array $content = array())
    {
        $this->_namespace = $namespace;
        if ($content) {
            foreach ($content as $key => $value) {
                $this->add($key, $value);
            }
        }
    }

    /**
     * 
     * @param string $namespace
     * @param array $content
     * @return \Flare\Registry
     */
    public static function create($namespace, array $content = array())
    {
        if (!isset(self::$_registry[$namespace])) {
            self::$_registry[$namespace] = new self($namespace, $content);
        } else {
            show_error('Namespace is already created');
        }
        return self::$_registry[$namespace];
    }

    /**
     * 
     * @param string $namespace
     * @return \Flare\Registry
     */
    public static function get($namespace)
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
     * @return \Flare\Registry
     */
    public function add($name, $object)
    {
        $this->_storage[$name] = $object;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function all()
    {
        return $this->_storage;
    }

    /**
     * 
     * @param string $name
     * @return array
     */
    public function fetch($name)
    {
        return isset($this->_storage[$name]) ? $this->_storage[$name] : null;
    }

    /**
     * 
     * @param string $name
     * @return \Flare\Registry
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
     * @return \Flare\Registry
     */
    public function clear()
    {
        $this->_storage = array();
        return $this;
    }
    
    /**
     * 
     * @param string $name
     * @param mixed $object
     * @return \Flare\Registry
     */
    public function push($name, $object)
    {
        if (!$this->has($name)) {
            $this->add($name, array($object));
        } else {
            $this->_storage[$name][] = $object;
        }
        return $this;
    }
}