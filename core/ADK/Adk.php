<?php

namespace ADK;

use ADK\Application\Config;
use ADK\Application\Mvc;
use ADK\Http\Request;
use ADK\Http\Response;
use ADK\Http\Session;
use ADK\Http\Uri;
use \Exception;

/**
 *
 * @author anthony
 *
 */
class Adk
{
    /**
     *
     * @var \ADK\Http\Request
     */
    public static $request = null;

    /**
     *
     * @var \ADK\Http\Response
     */
    public static $response = null;

    /**
     *
     * @var \ADK\Http\Uri
     */
    public static $uri = null;

    /**
     *
     * @var \ADK\Http\Session
     */
    public static $session = null;

    /**
     *
     * @var \ADK\Application\Config
     */
    public static $config = null;

    /**
     *
     * @var \ADK\Application\Mvc
     */
    private static $_mvc = null;

    /**
     *
     * @var array
     */
    private static $_db = array();

    /**
     *
     * @var boolean
     */
    private static $_init = false;

    /**
     *
     * @param string
     * @return void
     */
    public static function init($config_file)
    {
        if(self::$_init){
            return;
        }
        spl_autoload_register(function($class){
            if(strpos($class, 'ADK') === 0){
                require ADK_DIR.str_replace("\\", '/', $class).'.php';
            }
        });
        self::$config = Config::load($config_file);
        if(self::$config->time_limit !== null){
            set_time_limit(self::$config->time_limit);
        }
        if(self::$config->memory_limit !== null){
            ini_set('memory_limit', self::$config->memory_limit);
        }
        if(self::$config->timezone !== null){
            date_default_timezone_set(self::$config->timezone);
        }
        self::$request = new Request();
        self::$response = new Response();
        self::$uri = new Uri();
        if(self::$config->session['namespace']){
            self::$session = Session::getInstance(
                self::$config->session['namespace'],
                self::$config->session['auto_start']
            );
        } else {
            throw new Exception("Config[session][namespace] must be set");
        }

        if(!empty(self::$config->autoload['helpers'])){
            foreach(self::$config->autoload['helpers'] as $helper){
                self::helper($helper);
            }
        }
        
        self::$_init = true;
    }

    /**
     *
     * @param string $name
     * @param array $config
     * @return PDO
     */
    public static function db($name = 'default', $config = null)
    {
        if(!isset(self::$_db[$name])){
            if(!$config && !isset(self::$config->database[$name])){
                throw new Exception("Database configuration is not defined");
            }
            $config = self::$config->database[$name];
            $config['driver'] = strtolower($config['driver']);
            $dns = $config['driver'].':host='.$config['host'].';dbname='.$config['dbname'];
            $pdo = "\\ADK\\Db\\Sql\\Drivers\\".ucwords($config['driver']);
            self::$_db[$name] = new $pdo(
                $dns,
                $config['username'],
                $config['password'],
                $config['options']
            );
        }
        return self::$_db[$name];
    }

    /**
     *
     * @return mixed
     */
    public static function ns()
    {

    }

    /**
     *
     * @return \ADK\Application\Mvc
     */
    public static function mvc()
    {
        if(!self::$_mvc){
            self::$_mvc = new Mvc();
        }
        return self::$_mvc;
    }

    /**
     *
     * @param string $mashup
     * @return mixed
     */
    public static function mashup($mashup)
    {
        $mashup = "\\ADK\\Mashups\\{$mashup}";
        $mashup = new $mashup();
        return $mashup;
    }

    /**
     *
     * @param string $helper
     * @return void
     */
    public static function helper($helper)
    {
        $helper = ucwords(strtolower($helper));
        require_once ADK_DIR.'ADK/Helpers/'.$helper.'.php';
    }
}