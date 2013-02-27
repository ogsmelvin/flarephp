<?php

/**
 * 
 * @var string
 */
define('ADK_DIR', dirname(__FILE__).'/');

/**
 * 
 * @var string
 */
define('ADK_VERSION', '1.0');

if(!function_exists('debug')){

    /**
     * 
     * @param mixed $var
     * @param boolean $vardump
     * @return void
     */
    function debug($var, $vardump = false)
    {
        echo '<pre>';
        if(!$vardump){
            if(empty($var)){
                if(is_array($var)){
                    $var = "[[ Empty array ]]";
                } else if(is_string($var)){
                    $var = "[[ Empty string ]]";
                } else if(is_bool($var)){
                    $var = "[[ Bool: false ]]";
                }
            }
            print_r($var);
        } else {
            var_dump($var);
        }
        echo '</pre>';
    }
}

if(!function_exists('display_error')){

    /**
     * 
     * @param string $message
     * @return void
     */
    function display_error($message)
    {
        $disp = print_r($message, true);
        ADK\Adk::$response->setCode(500)
            ->setBody("<pre>{$disp}</pre>")
            ->send();
        exit;
    }
}

if(!class_exists("\\ADK\\Adk")){
    require ADK_DIR.'ADK/Adk.php';
}