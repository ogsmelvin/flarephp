<?php

namespace ADK\Objects;

use \ArrayObject;
use \Exception;

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
     */
    public function __construct($data = array())
    {
        if(is_string($data)){
            $data = json_decode($data, true);
            if(!is_array($data)){
                throw new Exception("Invalid JSON Format");
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