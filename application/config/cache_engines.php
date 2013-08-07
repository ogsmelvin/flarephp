<?php

/**
 * 
 * @var array
 */
$cache_engines = array(

	/**
	 * APC Configuration
	 */
	'Apc' => array(),

	/**
	 * Memcache Configuration
	 */
	'Memcache' => array(
		'host' => 'localhost',
		'port' => 1121
	),

	/**
	 * Memcached Configuration
	 */
	'Memcached' => array()
);

/**
 * 
 * @return array
 */
return $cache_engines;