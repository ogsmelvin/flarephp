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
$config['allow_override'] = false;

/**
 * Session configuration
 */
$config['session'] = array(
    'auto_start' => true,
    'namespace' => 'flare_dev_demo_session'
);

/**
 * Cookie configuration
 */
$config['cookie'] = array(
    'namespace' => 'flare_dev_demo_cookie',
    'enable_encryption' => true,
    'encryption_key' => '1q2w',
    'expiration' => 0
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
$config['cache_engines'] = require 'cache_engines.php';

/**
 *
 * @return array
 */
return $config;
