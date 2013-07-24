<?php

/**
 * constants
 */
require 'constants.php';

/**
 * Define modules
 */
$config['modules'] = array('main', 'demo');

/**
 * To return it to its default value
 * just give a value NULL
 */
$config['timezone'] = 'Asia/Manila';

/**
 * To return it to its default value
 * just give a value NULL
 */
$config['time_limit'] = null;

/**
 * To return it to its default value
 * just give a value NULL
 */
$config['memory_limit'] = null;

/**
 * Global XSS Filter
 */
$config['auto_xss_filter'] = false;

/**
 * GZIP output compression
 */
$config['auto_compress'] = true;

/**
 * Default response content type
 */
$config['default_content_type'] = 'text/html';

/**
 * Allow override of config in controller
 */
$config['allow_override'] = true;

/**
 * Session configuration
 */
$config['session'] = array(
    'auto_start' => true,
    'namespace' => 'flare.dev.demo'
);

/**
 * Cookie configuration
 */
$config['cookie'] = array(
    'namespace' => 'flare.dev.demo',
    'encryption_key' => '',
    'enable_encryption' => false
);


/**
 * View layout configuration
 */
$config['layout'] = require 'layout.php';

/**
 * Routing configuration
 */
$config['router'] = require 'routes.php';

/**
 * Autoload configuration
 */
$config['autoload'] = require 'autoload.php';

/**
 * Database configuration
 */
$config['database'] = require 'database.php';

/**
 * NoSql databases configuration
 */
$config['nosql'] = require 'nosql.php';

/**
 * Web Services / API Configuration
 */
$config['services'] = require 'services.php';

/**
 * Cache engines configuration
 */
$config['cache'] = require 'cache.php';

/**
 *
 * @return array
 */
return $config;
