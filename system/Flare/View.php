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

    private function __construct() {}

    /**
     * 
     * @param string $content
     * @return \Flare\View
     */
    public static function create($content = '')
    {
        $view = new self();
        $view->setContent($content);
        return $view;
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