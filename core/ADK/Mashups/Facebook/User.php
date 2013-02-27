<?php

namespace ADK\Mashups\Facebook;

use ADK\Mashups\Facebook;

/**
 * 
 * @author anthony
 * 
 */
class User
{
    /**
     * 
     * @var \ADK\Mashups\Facebook
     */
    private $_fb;

    /**
     * 
     * @param \ADK\Mashups\Facebook $fb
     */
    public function __construct(Facebook $fb)
    {
        $this->_fb = $fb;
    }

    /**
     * 
     * @param int $limit
     * @param int $page
     * @return array
     */
    public function getFriends($limit = null, $page = null)
    {
        $friends = array();
        return $friends;
    }
}