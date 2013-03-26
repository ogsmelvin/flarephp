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
define('APP_CORE_DIR', APP_DIR.'core/');
define('APP_TITLE', 'ADK Demo');

/**
 *
 * @var array
 */
$config['modules'] = array('main');

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
$config['time_limit'] = null;

/**
 * To return it to its default value
 * just give a value NULL
 * @var string
 */
$config['memory_limit'] = null;

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
    )
);

/**
 *
 * @var array
 */
$config['session'] = array(
    'auto_start'        => true,
    'namespace'         => 'adk_demo'
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
    'database'  => null,

    /**
     * Api Services Autoload
     * Keys are the same with the class names
     * These keys are case sensitive
     */
    'mashups' => array()
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
        'username'  => 'adk_demo',
        'password'  => 'tontonskie',
        'dbname'    => 'sample',
        'driver'    => 'mysql',
        'options'   => array(
            PDO::ATTR_PERSISTENT => true
        )
    )
);

/**
 * Api Services Configuration
 * Keys are the same with the class names
 * These keys are case sensitive
 * @var array
 */
$config['mashups'] = array(

    /**
     * Facebook settings
     */
    'Facebook'  => array(
        'app_id'        => '',
        'app_secret'    => '',
        'file_upload'   => true
    ),

    /**
     * Paypal settings
     */
    'Paypal'    => array(

    ),

    /**
     * 
     */
    'TextMagic' => array(
        'username' => '',
        'password' => ''
    ),

    /**
     * 
     */
    'BulkSMS' => array(
        'username' => '',
        'password' => ''
    )
);

/**
 *
 * @return array
 */
return $config;