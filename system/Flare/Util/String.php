<?php

namespace Flare\Util;

/**
 * 
 * @author anthony
 * 
 */
class String
{
    /**
     * 
     * @param string|array $value
     * @return string|array
     */
    public static function stripSlashes($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::stripSlashes($value[$key]);
            }
        } else {
            $value = stripslashes($value);
        }
        return $value;
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    public static function normalizeHeader($name)
    {
        $filtered = str_replace(array('-', '_'), ' ', (string) $name);
        $filtered = ucwords(strtolower($filtered));
        $filtered = str_replace(' ', '-', $filtered);
        return $filtered;
    }
}