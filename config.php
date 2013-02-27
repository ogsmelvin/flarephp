<?php

/**
 * constants
 * @var string
 */
define('APP_DIR', dirname(__FILE__).'/');
define('APP_ROOT_DIR', APP_DIR.'web/');
define('APP_MODULES_DIR', APP_DIR.'modules/');
define('APP_MODELS_DIR', APP_DIR.'models/');
define('APP_HELPERS_DIR', APP_DIR.'helpers/');
define('APP_CORE_DIR', dirname(APP_DIR).'/adk/core/');

/**
 *
 * @var array
 */
$config['modules'] = array('main', 'admin');

/**
 * To return it to its default value
 * just give a value NULL
 * @var string
 */
$config['timezone'] = 'Asia/Manila';

/**
 * To return it to its default value
 * just give a value NULL
 * @var int
 */
$config['time_limit'] = 0;

/**
 * To return it to its default value
 * just give a value NULL
 * @var string
 */
$config['memory_limit'] = '128M';

/**
 *
 * @var array
 */
$config['layout'] = array(

    /**
     * View Layout settings for main module
     */
    'main' => array(
        'auto'      => true,
        'layout'    => 'main'
    ),

    /**
     * View Layout settings for admin module
     */
    'admin' => array(
        'auto'      => true,
        'layout'    => 'admin'
    )
);

/**
 *
 * @var array
 */
$config['session'] = array(
    'auto_start'        => true,
    'namespace'         => 'election.dev'
);

/**
 *
 * @var boolean
 */
$config['allow_override'] = false;

/**
 *
 * @var array
 */
$config['router'] = array(

    /**
     * Custom url using the following format
     * :url => :module/:controller/:action
     *
     * ex.
     * 'routes' => array(
     *   '/welcome/home' => 'default/index/home'
     *  )
     *
     */
    'routes' => array(
        '/welcome/home' => 'default/index/home'
    ),

    /**
     * If you want to set your own error controllers
     * :module => :controller
     * this will automatically use 'errorAction' method
     * Just extend 'Lazarus_Controller_Error
     * and override 'errorAction' method.
     *
     * ex.
     * 'error_controllers' => array(
     *   'default' => 'myerror'
     *  )
     *
     */
    'error_controllers' => array(),

    /**
     * Default module, controller and action
     * to be loaded
     */
    'default_module'        => 'main',
    'default_controller'    => 'index',
    'default_action'        => 'index'
);

/**
 *
 * @var array
 */
$config['autoload'] = array(
    'helpers'   => array('text', 'view'),
    'models'    => array()
);

/**
 * Database configuration
 * @var array
 */
$config['database'] = array(

    /**
     * Default selected database
     */
    'default' => array(
        'host'      => 'localhost',
        'username'  => 'technoc9_dev',
        'password'  => 'vRavpq-e}QTA',
        'dbname'    => 'technoc9_election_2013',
        'driver'    => 'mysql',
        'options'   => array(
            PDO::ATTR_PERSISTENT => true
        )
    )
);

/**
 * Api Services Configuration
 * @var array
 */
$config['mashups'] = array(

    /**
     * Facebook settings
     */
    'facebook'  => array(
        'app_id'        => '',
        'app_secret'    => '',
        'file_upload'   => true
    ),

    /**
     * Paypal settings
     */
    'paypal'    => array(

    )
);

/**
 *
 * @return array
 */
return $config;