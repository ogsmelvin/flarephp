<?php

/**
 * Api Mashups Configuration
 * Keys are the same with the class names
 * These keys are case sensitive
 * @var array
 */
$services = array(

    /**
     * Flare\Services\Facebook settings
     */
    'Facebook'  => array(
        'app_id' => '',
        'app_secret' => '',
        'file_upload' => true
    ),

    /**
     * Flare\Services\OneWaySMS settings
     */
    'OneWaySMS' => array(
        'username' => '',
        'password' => '',
        'host' => ''
    )
);

/**
 * 
 * @return array
 */
return $services;