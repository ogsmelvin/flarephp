<?php

namespace Flare;

/**
 * 
 * @author anthony
 * 
 */
interface Singleton
{
    /**
     * 
     * @param array $params
     * @return mixed
     */
    public static function i(array $params = array());
}