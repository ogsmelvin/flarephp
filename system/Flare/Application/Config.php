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
     * @var string
     */
    const EXTENSION_NAME = 'php';

    /**
     * 
     * @var string
     */
    private static $_constantsFile = 'constants';

    /**
     * 
     * @var string
     */
    private static $_mainConfigFile = 'config';

    /**
     * 
     * @var array
     */
    private $_defaultKeyNames = array(
        'session' => false,
        'cookie' => false,
        'layout' => false,
        'router' => false,
        'autoload' => false,
        'database' => false,
        'nosql' => false,
        'services' => false,
        'cache_engines' => false,
        'mail' => false
    );

    /**
     * 
     * @var array
     */
    private $_loadedConfigNames = array();

    /**
     * 
     * @var string
     */
    private $_sourceDir;

    /**
     *
     * @var array
     */
    private $_config;

    /**
     *
     * @param array $config
     * @param string $sourceDir
     */
    private function __construct(array $config, $sourceDir)
    {
        foreach ($this->_defaultKeyNames as $name => $loaded) {
            if (file_exists($sourceDir.$name.'.'.self::EXTENSION_NAME)) {
                $config[$name] = (array) require $sourceDir.$name.'.'.self::EXTENSION_NAME;
                $this->_defaultKeyNames[$name] = true;
            }
        }
        $this->_config = $config;
        $this->_sourceDir = $sourceDir;
    }

    /**
     * 
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->_sourceDir;
    }

    /**
     *
     * @param string $config_dir
     * @param boolean $requireMainConfig
     * @return \Flare\Application\Config
     */
    public static function load($config_dir, $requireMainConfig = true)
    {
        if (!is_dir($config_dir)) {
            return null;
        }

        $config_dir = rtrim($config_dir, '/').'/';
        if (file_exists($config_dir.self::$_constantsFile.'.'.self::EXTENSION_NAME)) {
            require_once $config_dir.self::$_constantsFile.'.'.self::EXTENSION_NAME;
        }

        $content = array();
        if (file_exists($config_dir.self::$_mainConfigFile.'.'.self::EXTENSION_NAME)) {
            $content = (array) require_once $config_dir.self::$_mainConfigFile.'.'.self::EXTENSION_NAME;
        } elseif ($requireMainConfig) {
            die("'{$config_dir}config.php' file doesn't exists");
        }

        return new self($content, $config_dir);
    }

    /**
     * 
     * @param string $name
     * @return \Flare\Application\Config
     */
    public function read($name)
    {
        if (isset($this->_defaultKeyNames[$name]) && !$this->_defaultKeyNames[$name]) {
            if (file_exists($this->_sourceDir.$name.'.'.self::EXTENSION_NAME)) {
                $this->_config[$name] = (array) require $this->_sourceDir.$name.'.'.self::EXTENSION_NAME;
                $this->_defaultKeyNames[$name] = true;
            }
        } elseif (!in_array($name, $this->_loadedConfigNames)) {
            if (file_exists($this->_sourceDir.$name.'.'.self::EXTENSION_NAME)) {
                $this->_config[$name] = (array) require $this->_sourceDir.$name.'.'.self::EXTENSION_NAME;
                $this->_loadedConfigNames[] = $name;
            }
        }
        return $this;
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
        if (isset($this->_config['allow_override']) && !$this->_config['allow_override']) {
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
        if (isset($this->_config['allow_override']) && !$this->_config['allow_override']) {
            show_error('Config is not overridable');
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
        $conf = null;
        if (!isset($this->_config[$key])) {
            $key = explode('.', $key);
            $conf = $this->_config;
            foreach ($key as $k) {
                if (isset($conf[$k])) $conf = $conf[$k];
                else show_response(500, "'{$key}' doesn't exists in config");
            }
        } else {
            $conf = $this->_config[$key];
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
        if (!empty($this->_config['allow_override'])) {
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
     * @param \Flare\Application\Config|array $new
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