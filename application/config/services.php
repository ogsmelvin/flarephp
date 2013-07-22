<?php

/**
 * Api Mashups Configuration
 * Keys are the same with the class names
 * These keys are case sensitive
 * Keys are also arrange in the order defined in their constructor
 * Don't change the order
 * @var array
 */
$services = array(

    /**
     * FPHP\Services\Facebook settings
     */
    'Facebook'  => array(
        'app_id' => '',
        'app_secret' => '',
        'file_upload' => true
    ),

    /**
     * FPHP\Services\OneWaySMS settings
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