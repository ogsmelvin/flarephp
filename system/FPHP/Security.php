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
}