<?php

namespace Flare\Application;

/**
 *
 * @author anthony
 *
 */
class Config
{
    /**
     *
     * @var array
     */
    private $_config = array();

    /**
     *
     * @var \Flare\Application\Config
     */
    private static $_instance = null;

    /**
     *
     * @param array $config
     */
    private function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     *
     * @param string|array $config
     * @return \Flare\Application\Config
     */
    public static function load($config_file)
    {
        if(!self::$_instance){
            $content = null;
            if(is_string($config_file)){
                $config_file = rtrim($config_file, '.php').'.php';
                $content = require $config_file;
                if(!is_array($content)){
                    show_error("{$config_file} return must be an array");
                }
            } else if(is_array($config_file)){
                $content = $config_file;
                unset($config_file);
            } else {
                show_error("Invalid Config file type");
            }
            if(!isset($content['allow_override'])){
                $content['allow_override'] = false;
            }
            self::$_instance = new self($content);
        }
        return self::$_instance;
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if(!isset($this->_config[$key])){
            return null;
        }
        return $this->_config[$key];
    }

    /**
     *
     * @param string $key
     * @param string|array $value
     * @return void
     */
    public function __set($key, $value)
    {
        if(!$this->_config['allow_override']){
            return;
        }
        $this->_config[$key] = $value;
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \Flare\Application\Config
     */
    public function set($key, $value)
    {
        if(!$this->_config['allow_override']){
            return;
        }
        $key = explode('.', $key);
        $tmpConf = $this->_config;
        $conf = & $tmpConf;
        foreach($key as $k){
            if(isset($conf[$k])) $conf = & $conf[$k];
            else show_response(500, "'{$key}' doesn't exists in config");
        }
        $conf = $value;
        $this->_config = $tmpConf;
        unset($tmpConf, $conf);
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $key = explode('.', $key);
        $conf = $this->_config;
        foreach($key as $k){
            if(isset($conf[$k])) $conf = $conf[$k];
            else show_response(500, "'{$key}' doesn't exists in config");
        }
        return $conf;
    }

    /**
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_config[$key]);
    }

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_config;
    }
}