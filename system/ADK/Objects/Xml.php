<?php

namespace ADK\Objects;

use \SimpleXMLElement;

/**
 * 
 * @author anthony
 * 
 */
class Xml extends SimpleXMLElement
{
    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->asXml();
    }
}