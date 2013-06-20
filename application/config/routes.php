<?php

/**
 *
 * @var array
 */
$routes = array(

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
     * If you want to set your own error controllers
     * :module => :controller
     * this will automatically use 'errorAction' method
     * Just extend 'ErrorController'
     * and override 'errorAction' method.
     *
     * ex.
     * 'error_controllers' => array(
     *   'default' => 'myerror'
     *  )
     *
     */
    'error_controllers' => array(),

    /**
     * Default module, controller and action
     * to be loaded
     */
    'default_module'        => 'main',
    'default_controller'    => 'index',
    'default_action'        => 'index',

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