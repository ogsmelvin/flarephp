<?php

/**
 * constants
 */
require 'constants.php';

/**
 * Define modules
 */
$config['modules'] = array('main');

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
$config['session'] = require 'session.php';

/**
 * Cookie configuration
 */
$config['cookie'] = require 'cookie.php';

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
 * Mail Configuration
 */
$config['mail'] = require 'mail.php';

/**
 *
 * @return array
 */
return $config;
