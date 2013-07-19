<?php

/**
 * 
 * @var string
 */
define('FLARE_DIR', dirname(__FILE__).'/');

/**
 * 
 * @var string
 */
define('FLARE_VERSION', '1.0');

if (!function_exists('flare_load_class')) {

    /**
     * 
     * @author anthony
     * @param string $class
     * @return void
     */
    function flare_load_class($class)
    {
        if (strpos($class, 'Flare') === 0) {
            require FLARE_DIR.str_replace("\\", '/', $class).'.php';
        }
    }
}

if (!function_exists('debug')) {

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
        if (!$vardump) {
            if (empty($var)) {
                if (is_array($var)) {
                    $var = "[[ Empty array ]]";
                } elseif (is_string($var)) {
                    $var = "[[ Empty string ]]";
                } elseif (is_bool($var)) {
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

if (!function_exists('html')) {
    
    /**
     * 
     * @author anthony
     * @param string $string
     * @return string
     */
    function escape($string)
    {
        return \Flare\Security\Xss::filter($string);
    }
}

if (!function_exists('render')) {

    /**
     * 
     * @author anthony
     * @param string $path
     * @param array $data
     * @return string
     */
    function render($path, $data = array())
    {
        return \Flare\Flare::getApp()->view($path, $data, false);
    }
}

if (!function_exists('view')) {

    /**
     * 
     * @author anthony
     * @param string $viewModel
     * @return \Flare\Application\View\Model
     */
    function view($viewModel)
    {
        return \Flare\Flare::getApp()->getViewModelManager();
    }
}

$flare_sections = array();

if (!function_exists('section_open')) {

    /**
     * 
     * @author anthony
     * @param string $name
     * @return void
     */
    function section_open($name)
    {
        global $flare_sections;
        $flare_sections[$name] = true;
        ob_start();
    }
}

if (!function_exists('section_close')) {

    /**
     * 
     * @author anthony
     * @param string $name
     * @return void
     */
    function section_close($name)
    {
        global $flare_sections;
        if (!isset($flare_sections[$name])) {
            show_error("{$name} is not yet open");
        }
        $flare_sections[$name] = (string) ob_get_clean();
    }
}

if (!function_exists('get_section')) {

    /**
     * 
     * @author anthony
     * @param string $name
     * @return string
     */
    function get_section($name)
    {
        global $flare_sections;
        if (!isset($flare_sections[$name])) {
            return null;
        }
        return $flare_sections[$name];
    }
}

if (!function_exists('get_sections')) {

    /**
     * 
     * @author anthony
     * @return array
     */
    function get_sections()
    {
        global $flare_sections;
        return $flare_sections;
    }
}

if (!function_exists('http_build_url')) {

    /**
     * 
     * @author anthony
     * @param array $parsed_url
     * @return string
     */
    function http_build_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':'.$parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

if (!function_exists('get_image_types')) {

    /**
     * 
     * @author anthony
     * @return array
     */
    function get_image_types()
    {
        return array(
            IMAGETYPE_GIF,
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_SWF,
            IMAGETYPE_PSD,
            IMAGETYPE_BMP,
            IMAGETYPE_TIFF_II,
            IMAGETYPE_TIFF_MM,
            IMAGETYPE_JPC,
            IMAGETYPE_JP2,
            IMAGETYPE_JPX,
            IMAGETYPE_JB2,
            IMAGETYPE_SWC,
            IMAGETYPE_IFF,
            IMAGETYPE_WBMP,
            IMAGETYPE_XBM,
            IMAGETYPE_ICO
        );
    }
}

if (!function_exists('get_image_mime_types')) {

    /**
     * 
     * @author anthony
     * @return array
     */
    function get_image_mime_types()
    {
        return array_map('image_type_to_mime_type', get_image_types());
    }
}

if (!function_exists('show_response')) {

    /**
     * 
     * @author anthony
     * @param int $code
     * @param string $message
     * @return void
     */
    function show_response($code, $message = '')
    {
        Flare\Flare::getApp()->error($code, $message);
    }
}

if (!function_exists('show_error')) {

    /**
     * 
     * @author anthony
     * @param string $message
     * @return void
     */
    function show_error($message)
    {
        Flare\Flare::getApp()->error(500, $message);
    }
}

if (!function_exists('_flare_show_error')) {

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
    function _flare_show_error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        show_response(500, 'Error Code '.$errno.' : '.$errstr.' in line '.$errline.' : '.$errfile);
    }
}

if (!function_exists('flare')) {

    /**
     * 
     * @author anthony
     * @param mixed $object
     * @return mixed
     */
    function flare($object)
    {
        return $object;
    }
}

set_error_handler('_flare_show_error');
spl_autoload_register('flare_load_class');

if (!class_exists("\\Flare\\Flare")) {
    require FLARE_DIR.'Flare/Flare.php';
}



