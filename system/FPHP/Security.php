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
     * @var string
     */
    const FILTER_URI_CHARS = "a-z 0-9~%.:_\-";

    /**
     * 
     * @param string $str
     * @return string
     */
    public static function xssClean($str)
    {
        if(is_array($str)){
            return array_map(array('Security', 'xssClean'), $str);
        }
        return htmlspecialchars((string) $str, ENT_QUOTES);
    }

    /**
     * 
     * @param string $key
     * @param string $key
     * @param array $config
     * @return string
     */
    public static function encrypt($str, $key, $cipher = null, $mode = null)
    {
        if(!function_exists('mcrypt_encrypt')){
            display_error("Security::encrypt requires mcrypt library");
        }

        $result = null;
        if(empty($cipher)){
            $cipher = MCRYPT_RIJNDAEL_256;
        }
        if(empty($mode)){
            $mode = MCRYPT_MODE_CBC;
        }

        $key = pack('H*', $key);
        $iv_size = mcrypt_get_iv_size($cipher, $mode);
        if($iv_size){
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $result = mcrypt_encrypt($cipher, $key, $str, $mode, $iv);
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
    public static function decrypt($str, $key, $cipher = null, $mode = null)
    {
        if(!function_exists('mcrypt_decrypt')){
            display_error("Security::decrypt requires mcrypt library");
        }

        $result = null;
        if(empty($cipher)){
            $cipher = MCRYPT_RIJNDAEL_256;
        }
        if(empty($mode)){
            $mode = MCRYPT_MODE_CBC;
        }

        $key = pack('H*', $key);
        $iv_size = mcrypt_get_iv_size($cipher, $mode);
        if($iv_size){
            $str = base64_decode($str);
            $iv_dec = substr($str, 0, $iv_size);
            $str = substr($str, $iv_size);
            $result = mcrypt_decrypt($cipher, $key, $str, $mode, $iv_dec);
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

    /**
     * 
     * @param array $segments
     * @param string $characters
     * @return boolean
     */
    public static function validUriSegments(array $segments, $characters = null)
    {
        if(!$characters){
            $characters = self::FILTER_URI_CHARS;
        }
        foreach($segments as $segment){
            if(!self::validUriSegment($segment, $characters)){
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * @param string $segment
     * @param string $characters
     * @return boolean
     */
    public static function validUriSegment($segment, $characters = null)
    {
        if(!$characters){
            $characters = self::FILTER_URI_CHARS;
        }
        if(!empty($segment)){
            if(!preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($characters, '-'))."]+$|i", $segment)){
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * @param string $uri
     * @return string
     */
    public static function filterUriSegment($uri)
    {
        $bad = array('$', '(', ')', '%28', '%29');
        $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
        return str_replace($bad, $good, $uri);
    }

    /**
     * 
     * @param array $uris
     * @return array
     */
    public static function filterUriSegments($uris)
    {
        foreach($uris as &$u){
            $u = self::filterUriSegment($u);
        }
        return $uris;
    }

    /**
     * 
     * @param string $str
     * @param boolean $forUrl
     * @return string
     */
    public static function removeInvisibleChars($str, $forUrl = false)
    {
        $non_displayables = array();
        if($forUrl){
            $non_displayables[] = '/%0[0-8bcef]/';
            $non_displayables[] = '/%1[0-9a-f]/';
        }
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while($count);
        return $str;
    }
}