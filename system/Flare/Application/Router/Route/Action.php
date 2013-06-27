<?php

namespace FPHP\Application\Router\Route;

use FPHP\Application\AbstractController;
use \ReflectionException;
use \ReflectionMethod;

/**
 * 
 * @author anthony
 * 
 */
class Action extends ReflectionMethod
{
    /**
     * 
     * @var boolean
     */
    private $_isValid = true;

    /**
     * 
     * @param \FPHP\Application\AbstractController $controller
     * @param string $actionMethodName
     */
    public function __construct(AbstractController $controller, $actionMethodName)
    {
        try {
            parent::__construct($controller, $actionMethodName);
        } catch(ReflectionException $ex){
            $this->_isValid = false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function exists()
    {
        if(!$this->_isValid){
            return false;
        }
        return method_exists($this->class, $this->name);
    }
}