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
     * @author anthony
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
     * @author anthony
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

if(!function_exists('html')){
    
    /**
     * 
     * @author anthony
     * @param string $string
     * @return string
     */
    function escape($string)
    {
        return \FPHP\Security\Xss::filter($string);
    }
}

if(!function_exists('render')){

    /**
     * 
     * @author anthony
     * @param string $path
     * @param array $data
     * @return string
     */
    function render($path, $data = array())
    {
        return \FPHP\Fphp::getApp()->view($path, $data, false);
    }
}

$fphp_sections = array();

if(!function_exists('section_open')){

    /**
     * 
     * @author anthony
     * @param string $name
     * @return void
     */
    function section_open($name)
    {
        global $fphp_sections;
        $fphp_sections[$name] = true;
        ob_start();
    }
}

if(!function_exists('section_close')){

    /**
     * 
     * @author anthony
     * @param string $name
     * @return void
     */
    function section_close($name)
    {
        global $fphp_sections;
        if(!isset($fphp_sections[$name])){
            throw new Exception("{$name} is not yet open");
        }
        $fphp_sections[$name] = (string) ob_get_clean();
    }
}

if(!function_exists('get_section')){

    /**
     * 
     * @author anthony
     * @param string $name
     * @return string
     */
    function get_section($name)
    {
        global $fphp_sections;
        if(!isset($fphp_sections[$name])){
            return null;
        }
        return $fphp_sections[$name];
    }
}

if(!function_exists('get_sections')){

    /**
     * 
     * @author anthony
     * @return array
     */
    function get_sections()
    {
        global $fphp_sections;
        return $fphp_sections;
    }
}

if(!function_exists('http_build_url')){

    /**
     * 
     * @author anthony
     * @param array $parsed_url
     * @return string
     */
    function http_build_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}


if(!function_exists('show_response')){

    /**
     * 
     * @author anthony
     * @param int $code
     * @param string $message
     * @return void
     */
    function show_response($code, $message = '')
    {
        FPHP\Fphp::getApp()->error($code, $message);
    }
}

if(!function_exists('show_error')){

    /**
     * 
     * @author anthony
     * @param string $message
     * @return void
     */
    function show_error($message)
    {
        FPHP\Fphp::getApp()->error(500, $message);
    }
}

if(!function_exists('_fphp_show_error')){

    /**
     * 
     * @author anthony
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return void
     */
    function _fphp_show_error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        show_response(500, 'Error Code '.$errno.' : '.$errstr.' in line '.$errline.' : '.$errfile);
    }
}

if(!function_exists('fphp')){

    /**
     * 
     * @param mixed $object
     * @return mixed
     */
    function fphp($object)
    {
        return $object;
    }
}

set_error_handler('_fphp_show_error');
spl_autoload_register('fphp_load_class');

if(!class_exists("\\FPHP\\Fphp")){
    require FPHP_DIR.'FPHP/Fphp.php';
}



