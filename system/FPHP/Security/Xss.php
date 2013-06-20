<?php

namespace FPHP\Security;

use FPHP\Security;

/**
 * 
 * @author anthony
 * 
 */
class Xss extends Security
{
    /**
     * 
     * @param string|array $str
     * @return string
     */
    public static function filter($str)
    {
        if(is_array($str)){
            return array_map(array(__CLASS__, 'filter'), $str);
        }
        return htmlspecialchars((string) $str, ENT_QUOTES);
    }
}