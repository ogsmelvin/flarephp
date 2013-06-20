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

/**
 * 
 * @var string
 */
define('FPHP_AUTH_VALID', 'valid');

/**
 * 
 * @var string
 */
define('FPHP_AUTH_INVALID', 'invalid');

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
     * @param string $path
     * @param array $data
     * @return string
     */
    function render($path, $data = array())
    {
        return \FPHP\Fphp::mvc()->view($path, $data, false);
    }
}

$adk_sections = array();

if(!function_exists('section_open')){

    /**
     * 
     * @author anthony
     * @param string $name
     * @return void
     */
    function section_open($name)
    {
        global $adk_sections;
        $adk_sections[$name] = true;
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
        global $adk_sections;
        if(!isset($adk_sections[$name])){
            throw new Exception("{$name} is not yet open");
        }
        $adk_sections[$name] = (string) ob_get_clean();
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
        global $adk_sections;
        if(!isset($adk_sections[$name])){
            return null;
        }
        return $adk_sections[$name];
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
        global $adk_sections;
        return $adk_sections;
    }
}

if(!function_exists('http_build_url')){

    /**
     * 
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


if(!function_exists('display_error')){

    /**
     * 
     * @param string $message
     * @param int $code
     * @return void
     */
    function display_error($message, $code = 500)
    {
        $disp = print_r($message, true);
        FPHP\Fphp::$response->setCode($code)
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



