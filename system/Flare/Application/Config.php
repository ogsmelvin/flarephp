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
    private $_config;

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
     * @param string|array $config_file
     * @return \Flare\Application\Config
     */
    public static function load($config_file)
    {
        $content = null;
        if (is_string($config_file)) {
            $config_file = rtrim($config_file, '.php').'.php';
            $content = require $config_file;
            if (!is_array($content)) {
                show_error("{$config_file} return must be an array");
            }
        } elseif (is_array($config_file)) {
            $content = $config_file;
            unset($config_file);
        } else {
            show_error('Invalid Config file type');
        }
        if (!isset($content['allow_override'])) {
            $content['allow_override'] = false;
        }
        return new self($content);
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->_config[$key])) {
            return null;
        }
        return $this->_config[$key];
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (!$this->_config['allow_override']) {
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
        if (!$this->_config['allow_override']) {
            return;
        }
        $key = explode('.', $key);
        $tmpConf = $this->_config;
        $conf = & $tmpConf;
        foreach ($key as $k) {
            if (isset($conf[$k])) $conf = & $conf[$k];
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
        foreach ($key as $k) {
            if (isset($conf[$k])) $conf = $conf[$k];
            else show_response(500, "'{$key}' doesn't exists in config");
        }
        return $conf;
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Application\Config
     */
    public function remove($key)
    {
        if ($this->_config['allow_override']) {
            if (!isset($this->_config[$key])) {
                $key = explode('.', $key);
                $tmpConf = $this->_config;
                $conf = & $tmpConf;
                foreach ($key as $k) {
                    if (isset($conf[$k])) $conf = & $conf[$k];
                    else show_error("'{$key}' doesn't exists in config");
                }
                $conf = null;
                $this->_config = $tmpConf;
                unset($tmpConf, $conf);
            } else {
                unset($this->_config[$key]);
            }
        }
        return $this;
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
    public function all()
    {
        return $this->_config;
    }

    /**
     * 
     * @param \Flare\Application\Config|array $content
     * @return \Flare\Application\Config
     */
    public function merge($new)
    {
        if ($new instanceof Config) {
            $new = $new->all();
        }

        foreach ($this->_config as $key => &$config) {
            if (isset($new[$key])) $config = array_merge($config, $new[$key]);
        }
        return $this;
    }
}