<?php

namespace FPHP\UI\Html;

/**
 * 
 * @author anthony
 * 
 */
abstract class Element
{
    /**
     * 
     * @var mixed
     */
    protected $_data;

    /**
     * 
     * @var mixed
     */
    protected $_attrs = array();

    /**
     * 
     * @var string
     */
    protected $_name;

    /**
     * 
     * @param string $name
     * @param mixed $data
     */
    public function __construct($name, $data)
    {
        $this->setName($name);
        $this->setData($data);
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * 
     * @param array $attrs
     * @return \FPHP\UI\Html\Element
     */
    public function setAttributes(array $attrs)
    {
        $this->_attrs = $attrs;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \FPHP\UI\Html\Element
     */
    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attrs;
    }

    /**
     * 
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 
     * @param mixed $data
     * @return \FPHP\UI\Html\Element
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * 
     * @return string
     */
    abstract public function render();
}