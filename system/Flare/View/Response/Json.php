<?php

namespace Flare\View\Response;

use Flare\Objects\Json as JsonObject;
use Flare\View\Response;

/**
 * 
 * @author anthony
 * 
 */
class Json extends Response
{
    /**
     * 
     * @var string
     */
    protected $contentType = 'application/json';

    /**
     * 
     * @var \Flare\Objects\Json
     */
    private $json;

    /**
     * 
     * @param \Flare\Objects\Json|array|string $content
     * @param boolean $is_url
     */
    public function __construct($content)
    {
        if (!($content instanceof JsonObject)) {
            $content = new JsonObject($content);
        }
        $this->json = $content;
    }

    /**
     * 
     * @return string
     */
    public function render()
    {
        return (string) $this->json;
    }
}