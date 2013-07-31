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
    public static $request;

    /**
     *
     * @var \Flare\Http\Response
     */
    public static $response;

    /**
     *
     * @var \Flare\Http\Uri
     */
    public static $uri;

    /**
     *
     * @var \Flare\Http\Session
     */
    public static $session;

    /**
     *
     * @var \Flare\Http\Cookie
     */
    public static $cookie;

    /**
     *
     * @var \Flare\Application\Config
     */
    public static $config;

    /**
     * 
     * @var \Flare\Application\Router
     */
    public static $router;

    /**
     *
     * @var \Flare\Application
     */
    private static $_application;

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
        $conf = & self::$config;
        if ($conf->time_limit !== null) {
            set_time_limit($conf->time_limit);
        }
        if ($conf->memory_limit !== null) {
            ini_set('memory_limit', $conf->memory_limit);
        }
        if ($conf->timezone !== null) {
            date_default_timezone_set($conf->timezone);
        }
        
        self::$request = new Request();
        self::$response = new Response();
        self::$uri = new Uri();
        
        $routes = array();
        if ($conf->router['routes']) {
            $routes = $conf->router['routes'];
        }
        self::$router = new Router($routes);

        if ($conf->session['namespace']) {
            self::$session = Session::create(
                $conf->session['namespace'],
                $conf->session['auto_start']
            );
        } else {
            show_error('Config[session][namespace] must be set');
        }

        if ($conf->cookie['namespace']) {
            if ($conf->cookie['enable_encryption'] && !$conf->cookie['encryption_key']) {
                show_error('Config[encryption_key] must be set');
            }
            self::$cookie = Cookie::create(
                $conf->cookie['namespace'],
                $conf->cookie['expiration'],
                $conf->cookie['enable_encryption'] ? $conf->cookie['encryption_key'] : false
            );
        } else {
            show_error('Config[cookie][namespace] must be set');
        }

        if ($conf->router['require_https']) {
            self::$router->secure();
        }
        if ($conf->auto_compress && !@ini_get('zlib.output_compression')
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
            show_error('Flare Application is already created');
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