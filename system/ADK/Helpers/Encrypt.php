<?php

if(!function_exists('adk_encrypt')){

    /**
     * 
     * @param string $sData
     * @param string $sKey
     * @return string
     */
    function adk_encrypt($sData, $sKey)
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
}

if(!function_exists('adk_decrypt')){

    /**
     * 
     * @param string $sData
     * @param string $sKey
     * @return string
     */
    function adk_decrypt($sData, $sKey)
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
}

