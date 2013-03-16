<?php

namespace ADK\Application;

use ADK\Http\Request;
use ADK\Http\Response;
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
     * @var \ADK\Http\Request
     */
    protected $request;

    /**
     * 
     * @var \ADK\Http\Response
     */
    protected $response;

    /**
     * 
     * @var \ADK\Http\Uri
     */
    protected $uri;

    /**
     * 
     * @param \ADK\Http\Request $request
     * @param \ADK\Http\Response $response
     */
    public function __construct(Request &$request, Response &$response)
    {
        $this->session = & A::$session;
        $this->config = & A::$config;
        $this->uri = & A::$uri;
        $this->request = & $request;
        $this->response = & $response;

        if($this->config->autoload['database']){
            $this->setDb($this->config->autoload['database']);
        }
    }

    /**
     * 
     * @param string $mashup
     * @param string $shortKey
     * @return void
     */
    public function setMashup($mashup, $shortKey = null)
    {
        if(!$shortKey){
            $shortKey = $mashup;
        }
        if(isset($this->{$shortKey})){
            return $this->{$shortKey};
        }
        $this->{$shortKey} = A::mashup($mashup);
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