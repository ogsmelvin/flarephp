<?php

/**
 * 
 * @var array
 */
$autoload = array(

    /**
     * Only one database can be set 
     * for autoload
     * Set an empty value to turn off autoload of database
     */
    'database' => 'default',

    /**
     * Helpers Autoload
     * Keys are the same with the class names
     * These keys are case sensitive
     */
    'helpers' => array(),

    /**
     * Api Services Autoload
     * Keys are the same with the class names
     * These keys are case sensitive
     */
    'services' => array(),

    /**
     * Cache engines Autoload
     * Keys are the same with the class names
     * These keys are case sensitive
     */
    'cache' => array()
);

/**
 * 
 * @return array
 */
return $autoload;