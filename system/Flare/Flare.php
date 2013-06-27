<?php

namespace Flare;

use Flare\Application\Config;
use Flare\Application\Router;
use Flare\Http\Response;
use Flare\Http\Request;
use Flare\Http\Session;
use Flare\Application;
use \ReflectionClass;
use Flare\Http\Uri;

/**
 *
 * @author anthony
 *
 */
class Flare
{
    /**
     *
     * @var \Flare\Http\Request
     */
    public static $request = null;

    /**
     *
     * @var \Flare\Http\Response
     */
    public static $response = null;

    /**
     *
     * @var \Flare\Http\Uri
     */
    public static $uri = null;

    /**
     *
     * @var \Flare\Http\Session
     */
    public static $session = null;

    /**
     *
     * @var \Flare\Application\Config
     */
    public static $config = null;

    /**
     * 
     * @var \Flare\Application\Router
     */
    public static $router = null;

    /**
     *
     * @var \Flare\Application
     */
    private static $_application = null;

    /**
     *
     * @var array
     */
    private static $_db = array();

    /**
     * 
     * @var array
     */
    private static $_ns = array();

    /**
     * 
     * @var array
     */
    private static $_services = array();

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
        
        $routes = array();
        if(self::$config->router['routes']){
            $routes = self::$config->router['routes'];
        }
        self::$router = new Router($routes);

        if(self::$config->session['namespace']){
            self::$session = Session::getInstance(
                self::$config->session['namespace'],
                self::$config->session['auto_start']
            );
        } else {
            show_error("Config[session][namespace] must be set");
        }
        if(self::$config->require_https){
            self::$router->secure();
        }
        if(self::$config->auto_compress && !@ini_get('zlib.output_compression')
            && extension_loaded('zlib') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
            && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE){
            if(!ob_start('ob_gzhandler')){
                show_response(500, 'output compression failed');
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
    public static function & db($name = 'default', $config = null)
    {
        if(!isset(self::$_db[$name])){
            if(!$config){
                if(!isset(self::$config->database[$name])){
                    show_error("No database configuration found");
                }
                $config = self::$config->database[$name];
            }
            $config['driver'] = strtolower($config['driver']);
            $dns = $config['driver'].':host='.$config['host'].';dbname='.$config['dbname'];
            $pdo = "\\Flare\\Db\\Sql\\Drivers\\".ucwords($config['driver']);
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
     * @param string $key
     * @param array $config
     * @return mixed
     */
    public static function & ns($key, $config = null)
    {
        if(!isset(self::$_ns[$key])){
            if(!$config){
                if(!isset(self::$config->nosql[$key])){
                    show_error("Config for service '{$key}' is not defined");
                }
                $config = self::$config->nosql[$key];
            }

            $ref = new ReflectionClass("\\Flare\\Db\\Nosql\\".$key);
            self::$_ns[$key] = $ref->newInstanceArgs($config);
        }
        return self::$_ns[$key];
    }

    /**
     * 
     * @return \Flare\Application
     */
    public static function createApp()
    {
        if(self::$_application && self::$_application instanceof Application){
            show_error("Flare Application is already created");
        }
        self::$_application = new Application();
        return self::$_application;
    }

    /**
     *
     * @return \Flare\Application
     */
    public static function getApp()
    {
        return self::$_application;
    }

    /**
     *
     * @param string $service
     * @param array $config
     * @return mixed
     */
    public static function service($service, $config = null)
    {
        if(!isset(self::$_services[$service])){
            if(!$config){
                if(!isset(self::$config->services[$service])){
                    show_error("Config for service '{$service}' is not defined");
                }
                $config = self::$config->services[$service];
            }

            $ref = new ReflectionClass("\\Flare\\Services\\".$service);
            self::$_services[$service] = $ref->newInstanceArgs($config);
        }
        return self::$_services[$service];
    }
}
