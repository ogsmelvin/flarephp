<?php

/**
 * Simple function to replicate PHP 5 behaviour
 */
// function microtime_float()
// {
//     list($usec, $sec) = explode(" ", microtime());
//     return ((float)$usec + (float)$sec);
// }

// $time_start = microtime_float();


/**
 * Config file inclusion
 * Don't remove this
 */
$config = require dirname(dirname(__FILE__)).'/config.php';

#### ADK inclusion ####

require APP_CORE_DIR.'adk.php';
use ADK\Adk as A;

###### Start app ######

A::init($config);
$mvc = A::mvc();
$mvc->setModules($config['modules'])
    ->setModulesDirectory(APP_MODULES_DIR)
    ->setModelsDirectory(APP_MODELS_DIR)
    ->setLayoutsDirectory(APP_DIR.'layouts')
    ->setControllersDirectory('controllers')
    ->setViewsDirectory('views')
    ->preDispatch();

###### Predispatch ######

###### Dispatch #########

$mvc->dispatch();

###### Postdispatch ######

// $time_end = microtime_float();
// // $time = $time_end - $time_start;
// $time = number_format($time_end - $time_start, 4);
// echo "<br><div class=\"alert alert-info\">Page rendered in <b>{$time} seconds</b></div>";
