<?php

/**
 * constants
 */
require 'constants.php';

/**
 *
 * @var array
 */
$config['modules'] = array('main', 'demo');

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
$config['auto_xss_filter'] = false;

/**
 * 
 * @var boolean
 */
$config['auto_compress'] = true;

/**
 * 
 * @var boolean
 */
$config['require_https'] = false;

/**
 * 
 * @var string
 */
$config['default_content_type'] = 'text/html';

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

    'demo' => array(
        'auto'      => true,
        'layout'    => 'demo'
    )
);

/**
 *
 * @var array
 */
$config['session'] = array(
    'auto_start'        => true,
    'namespace'         => 'flare.dev.demo'
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
        'dbname'    => 'technoc9_sample',
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
 *
 * @var array
 */
$config['services'] = require 'services.php';

/**
 *
 * @return array
 */
return $config;
