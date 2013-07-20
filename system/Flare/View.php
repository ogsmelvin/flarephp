<?php

namespace Flare;

/**
 * 
 * @author anthony
 * 
 */
class View
{
    /**
     * 
     * @var string
     */
    private $content;

    /**
     * 
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->setContent($content);
    }

    /**
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 
     * @param string $content
     * @return \Flare\View
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}