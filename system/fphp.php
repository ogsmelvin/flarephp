<?php

/**
 * 
 * @var string
 */
define('FPHP_DIR', dirname(__FILE__).'/');

/**
 * 
 * @var string
 */
define('FPHP_VERSION', '1.0');

if(!function_exists('fphp_load_class')){

    /**
     * 
     * @param string $class
     * @return void
     */
    function fphp_load_class($class)
    {
        if(strpos($class, 'FPHP') === 0){
            require FPHP_DIR.str_replace("\\", '/', $class).'.php';
        }
    }
}

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
        FPHP\Fphp::$response->setCode(500)
            ->setBody("<pre>{$disp}</pre>")
            ->send();
        exit;
    }
}

if(!function_exists('_fphp_show_error')){

    /**
     * 
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return void
     */
    function _fphp_show_error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        display_error('Error Code '.$errno.' : '.$errstr.' in line '.$errline.' : '.$errfile);
    }
}

set_error_handler('_fphp_show_error');
spl_autoload_register('fphp_load_class');

if(!class_exists("\\FPHP\\Fphp")){
    require FPHP_DIR.'FPHP/Fphp.php';
}



