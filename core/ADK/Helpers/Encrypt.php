<?php

if(!function_exists('adk_encrypt')){

    /**
     * 
     * @param string $key
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
        $sBase64 = base64_encode($sResult);
        return strtr($sBase64, '+/', '-_');
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
        $sResult = '';
        $sData   = $this->decode_base64($sData);
        for($i = 0; $i < strlen($sData); $i++){
            $sChar    = substr($sData, $i, 1);
            $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
            $sChar    = chr(ord($sChar) - ord($sKeyChar));
            $sResult .= $sChar;
        }
        return $sResult;
    }
}
