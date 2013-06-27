<?php

namespace Flare\Objects;

use \ArrayObject;

/**
 * 
 * @author anthony
 * 
 */
class Json extends ArrayObject
{
    /**
     * 
     * @param string|array $data
     * @param boolean $is_url
     */
    public function __construct($data = array(), $is_url = false)
    {
        if(is_string($data)){
            if($is_url){
                $data = @file_get_contents($data);
                if($data === false){
                    show_error("Error encountered accessing JSON URL");
                }
            }
            $data = json_decode($data, true);
            if(!is_array($data)){
                show_error("Invalid JSON Format");
            }
        }
        parent::__construct($data);
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return json_encode($this);
    }
}