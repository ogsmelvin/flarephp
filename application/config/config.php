<?php

/**
 * constants
 */
require 'constants.php';

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
 * @var boolean
 */
$config['auto_xss_filtering'] = false;

/**
 * 
 * @var boolean
 */
$config['auto_compress'] = true;

/**
 * 
 * @var boolean
 */
$config['require_https'] = true;

/**
 *
 * @var array
 */
$config['layout'] = array(

    /**
     * View Layout settings for main module
     */
    'main' => array(
        'auto'      => false,
        'layout'    => 'main'
    )
);

/**
 *
 * @var array
 */
$config['session'] = array(
    'auto_start'        => true,
    'namespace'         => 'demo.dev'
);

/**
 *
 * @var boolean
 */
$config['allow_override'] = true;

/**
 *
 * @var array
 */
$config['router'] = require 'routes.php';

/**
 *
 * @var array
 */
$config['autoload'] = array(

    'helpers'   => array(),

    /**
     * 
     * Only one database can be set 
     * for autoload
     * Set an empty value to turn off autoload of database
     */
    'database'  => 'default',

    /**
     * Api Services Autoload
     * Keys are the same with the class names
     * These keys are case sensitive
     */
    'services' => array()
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
        'password'  => 't3chn0c9_d3v',
        'dbname'    => 'technoc9_jarvis',
        'driver'    => 'mysql',
        'options'   => array(
            PDO::ATTR_PERSISTENT => true
        )
    )
);

/**
 * NO Sql databases
 * Keys are the same with the class names
 * These keys are case sensitive
 * @var array
 */
$config['nosql'] = array(
    'Mongodb' => array()
);

/**
 * Api Mashups Configuration
 * Keys are the same with the class names
 * These keys are case sensitive
 * @var array
 */
$config['services'] = array(

    /**
     * ADK\Services\Facebook settings
     */
    'Facebook'  => array(
        'app_id'        => '',
        'app_secret'    => '',
        'file_upload'   => true
    ),

    /**
     * ADK\Services\OneWaySMS settings
     */
    'OneWaySMS' => array(
        'username' => '',
        'password' => ''
    )
);

/**
 *
 * @return array
 */
return $config;
