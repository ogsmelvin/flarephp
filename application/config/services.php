<?php

/**
 * Api Mashups Configuration
 * Keys are the same with the class names
 * These keys are case sensitive
 * @var array
 */
$services = array(

	/**
	 * Flare\Service\Facebook settings
	 */
	'Facebook'  => array(
		'app_id' => '',
		'app_secret' => '',
		'file_upload' => true
	),

	/**
	 * Flare\Service\OneWaySMS settings
	 */
	'OneWaySMS' => array(
		'username' => '',
		'password' => '',
		'host' => ''
	),

	/**
	 * Flare\Service\Twitter settings
	 */
	'Twitter' => array(
		'consumer_key' => '',
		'consumer_secret' => ''
	)
);

/**
 * 
 * @return array
 */
return $services;