<?php

if(!function_exists('title_case')){

    /**
     * 
     * @author anthony
     * @param string $string
     * @return string
     */
    function title_case($string)
    {
        return ucwords(str_replace('_', ' ', strtolower($string)));
    }
}

if(!function_exists('format_number')){

    /**
     * 
     * @author anthony
     * @param string|int $number
     * @param string $thousand_sep
     * @param string $dec_sep
     * @return string
     */
    function format_number($number, $decimals = 0, $dec_sep = '.', $thousand_sep = ',')
    {
        return number_format($number, $decimals, $dec_sep, $thousand_sep);
    }
}

if(!function_exists('format_date')){
    
    /**
     * Format date from MySql format to word format
     * @author anthony
     * @param string $date
     * @param string $format
     * @return string
     */
    function format_date($date, $format = "F d, Y")
    {
        $hour = 0;
        $min = 0;
        $sec = 0;
        $date = explode('-', $date);
        $day = isset($date[2]) ? $date[2] : '00';
        if(strlen($day) > 2){
            $day = explode(' ', $day);
            if(isset($day[1])){
                $time = explode(':', $day[1]);
                $hour = (int) $time[0];
                $min = (int) $time[1];
                $sec = (int) $time[2];
            }
            $day = $day[0];
        }
        $month = isset($date[1]) ? $date[1] : '00';
        $year = isset($date[0]) ? $date[0] : '0000';
        return date($format, mktime($hour, $min, $sec, $month, $day, $year));
    }
}

if(!function_exists('unique_code')){

    /**
     * 
     * @author anthony
     * @param int $length
     * @return string
     */
    function unique_code($length = 8)
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $length));
    }
}