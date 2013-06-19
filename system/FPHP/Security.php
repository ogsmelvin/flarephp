<?php

namespace FPHP;

/**
 * 
 * @author anthony
 * 
 */
class Security
{
    /**
     * 
     * @param string $str
     * @return string
     */
    public static function xssClean($str)
    {
        return htmlspecialchars((string) $str, ENT_QUOTES);
    }

    /**
     * 
     * @param string $key
     * @param string $key
     * @param array $config
     * @return string
     */
    public static function encrypt($str, $key, $config = array())
    {
        if(!function_exists('mcrypt_encrypt')){
            display_error("Security::encrypt requires mcrypt library");
        }

        $result = null;
        if(empty($config['cipher'])){
            $config['cipher'] = MCRYPT_RIJNDAEL_256;
        }
        if(empty($config['mode'])){
            $config['mode'] = MCRYPT_MODE_CBC;
        }

        $key = pack('H*', $key);
        $iv_size = mcrypt_get_iv_size($config['cipher'], $config['mode']);
        if($iv_size){
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $result = mcrypt_encrypt($config['cipher'], $key, $str, $config['mode'], $iv);
            $result = base64_encode($iv.$result);
        }

        return $result;
    }

    /**
     * 
     * @param string $str
     * @param string $key
     * @param array $config
     * @return string
     */
    public static function decrypt($str, $key, $config = array())
    {
        if(!function_exists('mcrypt_decrypt')){
            display_error("Security::decrypt requires mcrypt library");
        }

        $result = null;
        if(empty($config['cipher'])){
            $config['cipher'] = MCRYPT_RIJNDAEL_256;
        }
        if(empty($config['mode'])){
            $config['mode'] = MCRYPT_MODE_CBC;
        }

        $key = pack('H*', $key);
        $iv_size = mcrypt_get_iv_size($config['cipher'], $config['mode']);
        if($iv_size){
            $str = base64_decode($str);
            $iv_dec = substr($str, 0, $iv_size);
            $str = substr($str, $iv_size);
            $result = mcrypt_decrypt($config['cipher'], $key, $str, $config['mode'], $iv_dec);
        }

        return $result;
    }

    /**
     * 
     * @param string $str
     * @return string
     */
    public static function hash($str)
    {
        return sha1($str);
    }

    /**
     * 
     * @param int $length
     * @return string
     */
    public static function uniqueCode($length = 8)
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $length));
    }
}