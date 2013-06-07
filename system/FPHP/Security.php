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
     * @param string $sData
     * @param string $sKey
     * @return string
     */
    public static function encrypt($sData, $sKey)
    {
        $sResult = '';
        for($i = 0; $i < strlen($sData); $i++){
            $sChar    = substr($sData, $i, 1);
            $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
            $sChar    = chr(ord($sChar) + ord($sKeyChar));
            $sResult .= $sChar;
        }
        return strtr(base64_encode($sResult), '+/', '-_');
    }

    /**
     * 
     * @param string $sData
     * @param string $sKey
     * @return string
     */
    public static function decrypt($sData, $sKey)
    {
        $sData = base64_decode(strtr($sData, '-_', '+/'));
        for($i = 0; $i < strlen($sData); $i++){
            $sChar    = substr($sData, $i, 1);
            $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
            $sChar    = chr(ord($sChar) - ord($sKeyChar));
            $sResult .= $sChar;
        }
        return $sResult;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public static function hash($str)
    {
        return md5($str);
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