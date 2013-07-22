<?php

/**
 *
 * @var array
 */
$routes = array(

    /**
     * 
     * Force HTTPS
     */
    'require_https' => false,

    /**
     * Custom url using the following format
     * :url => module.controller.action
     *
     * ex.
     * 'routes' => array(
     *   '/welcome/home' => 'module.controller.action'
     *  )
     *
     */
    'routes' => array(
        '/welcome/home' => 'module.controller.action'
    ),

    /**
     * 
     * to use the built in just leave the value blank
     * statusCode => 'module.controller.action'
     */
    'errors' => array(
        404 => 'module.controller.action',
        500 => 'module.controller.action'
    ),

    /**
     * Default module, controller and action
     * to be loaded
     */
    'default_module' => 'main',
    'default_controller' => 'index',
    'default_action' => 'index',

    /**
     * Leave blank for default
     * /home.[url_suffix]
     */
    'url_suffix'    => 'jspx'
);

/**
 * 
 * @return array
 */
return $routes;