<?php

namespace Flare\Application\Http;

use Flare\Http\Response as ParentResponse;

/**
 * 
 * @author anthony
 * 
 */
class Response extends ParentResponse
{
    /**
     * 
     * @param string $name
     * @param string $value
     * @return \Flare\Application\Http\Response
     */
    public function addCookie($name, $value)
    {
        setcookie($name, $value);
        return $this;
    }

    /**
     * 
     * @param array $cookies
     * @return \Flare\Application\Http\Response
     */
    public function addCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie['name'], $cookie['value']);
        }
        return $this;
    }
}