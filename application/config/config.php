<?php

/**
 * constants
 */
require 'constants.php';

/**
 * Define modules
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
 * @var boolean
 */
$config['allow_override'] = true;

/**
 *
 * @var array
 */
$config['session'] = array(
    'auto_start' => true,
    'namespace' => 'flare.dev.demo'
);

/**
 * View layout configuration
 * @var array
 */
$config['layout'] = require 'layout.php';

/**
 * Routing configuration
 * @var array
 */
$config['router'] = require 'routes.php';

/**
 * Autoload configuration
 * @var array
 */
$config['autoload'] = require 'autoload.php';

/**
 * Database configuration
 * @var array
 */
$config['database'] = require 'database.php';

/**
 * NoSql databases configuration
 * @var array
 */
$config['nosql'] = require 'nosql.php';

/**
 * Web Services / API Configuration
 * @var array
 */
$config['services'] = require 'services.php';

/**
 * Cache engines configuration
 * @var array
 */
$config['cache'] = require 'cache.php';

/**
 *
 * @return array
 */
return $config;
