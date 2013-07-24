<?php

namespace Flare;

use Flare\Application\Config;
use Flare\Application\Router;
use Flare\Http\Response;
use Flare\Http\Request;
use Flare\Http\Session;
use Flare\Http\Cookie;
use Flare\Application;
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
     * @var \Flare\Http\Cookie
     */
    public static $cookie = null;

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
        if (self::$_init) {
            return;
        }

        self::$config = Config::load($config_file);
        if (self::$config->time_limit !== null) {
            set_time_limit(self::$config->time_limit);
        }
        if (self::$config->memory_limit !== null) {
            ini_set('memory_limit', self::$config->memory_limit);
        }
        if (self::$config->timezone !== null) {
            date_default_timezone_set(self::$config->timezone);
        }
        
        self::$request = new Request();
        self::$response = new Response();
        self::$uri = new Uri();
        
        $routes = array();
        if (self::$config->router['routes']) {
            $routes = self::$config->router['routes'];
        }
        self::$router = new Router($routes);

        if (self::$config->session['namespace']) {
            self::$session = Session::getInstance(
                self::$config->session['namespace'],
                self::$config->session['auto_start']
            );
        } else {
            show_error("Config[session][namespace] must be set");
        }

        if (self::$config->cookie) {
            if (self::$config->cookie['enable_encryption'] && !self::$config->cookie['encryption_key']) {
                show_error('Config[encryption_key] must be set');
            }
            self::$cookie = Cookie::getInstance(
                self::$config->cookie['enable_encryption'],
                self::$config->cookie['encryption_key']
            );
        }

        if (self::$config->router['require_https']) {
            self::$router->secure();
        }
        if (self::$config->auto_compress && !@ini_get('zlib.output_compression')
            && extension_loaded('zlib') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
            && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
        {
            if (!ob_start('ob_gzhandler')) {
                show_response(500, 'output compression failed');
            }
        }
        
        self::$_init = true;
    }
    
    /**
     * 
     * @return \Flare\Application
     */
    public static function createApp()
    {
        if (self::$_application) {
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
}