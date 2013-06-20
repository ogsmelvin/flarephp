<?php

namespace FPHP\Security;

use FPHP\Security;

/**
 * 
 * @author anthony
 * 
 */
class Hash extends Security;
{
    /**
     * 
     * @param string $str
     * @return string
     */
    public static function create($str)
    {
        return sha1($str);
    }
}