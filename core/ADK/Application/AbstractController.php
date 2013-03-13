<?php

namespace ADK\Application;

use ADK\Adk as A;

/**
 * 
 * @author anthony
 * 
 */
abstract class AbstractController
{
    /**
     * 
     * @var \ADK\Http\Session
     */
    protected $session;

    /**
     * 
     * @var \ADK\Application\Config
     */
    protected $config;

    /**
     * 
     * @var \ADK\Http\Uri
     */
    protected $uri;

    public function __construct()
    {
        $this->session = & A::$session;
        $this->config = & A::$config;
        $this->uri = & A::$uri;

        if($this->config->autoload['database']){
            $this->setDb($this->config->autoload['database']);
        }
    }

    /**
     * 
     * @param string $mashup
     * @param string $shortKey
     * @return mixed
     */
    public function mashup($mashup, $shortKey = null)
    {
        if(!$shortKey){
            $shortKey = $mashup;
        }
        if(isset($this->{$shortKey})){
            return $this->{$shortKey};
        }
        $this->{$shortKey} = A::mashup($mashup);
        return $this->{$shortKey};
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function setDb($key = 'default')
    {
        $this->db = A::db($key);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function setNosql($key)
    {
        $this->{$key} = A::ns($key);
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @param string|boolean $layout
     * @return mixed
     */
    public function view($path, $data = null, $layout = null)
    {
        return A::mvc()->view($path, $data, $layout);
    }

    /**
     * 
     * @return void
     */
    abstract public function init();

    /**
     * 
     * @return void
     */
    abstract public function complete();
}