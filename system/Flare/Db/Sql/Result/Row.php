<?php

namespace Flare\Db\Sql\Result;

use Flare\Db\Sql\Query\ARQuery;
use Flare\Security\Xss;

/**
 * 
 * @author anthony
 * 
 */
class Row
{
    /**
     * 
     * @var \Flare\Db\Sql\Query\ARQuery
     */
    private $_query;

    /**
     * 
     * @var string|int
     */
    private $_id;

    /**
     * 
     * @var string
     */
    private $_table;

    /**
     * 
     * @var string
     */
    private $_pk;

    /**
     * 
     * @var array
     */
    private $_data = array();

    /**
     * 
     * @param \Flare\Db\Sql\Query\ARQuery $query
     * @param string $table
     * @param string|int $pk
     * @param string|int $id
     */
    public function __construct(ARQuery &$query, $table = null, $pk = null, $id = null)
    {
        $this->_query = & $query;
        $this->_table = $table;
        $this->_pk = $pk;
        $this->_id = $id;
    }

    /**
     * 
     * @param string $table
     * @return \Flare\Db\Sql\Result\Row
     */
    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \Flare\Db\Sql\Result\Row
     */
    public function setPrimaryKey($key)
    {
        $this->_pk = $key;
        return $this;
    }

    /**
     * 
     * @return string|int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * 
     * @param string|int $id
     * @return \Flare\Db\Sql\Result\Row
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * 
     * @param array $data
     * @return \Flare\Db\Sql\Result\Row
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->_data[$key])) {
            show_error("'{$key}' doesn't exist in the row object");
        }
        return $this->_data[$key];
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * 
     * @param string $field
     * @return int
     */
    public function int($field)
    {
        return intval($this->__get($field));
    }

    /**
     * 
     * @param string $field
     * @param boolean $xss
     * @return string
     */
    public function string($field, $xss = true)
    {
        if ($xss) {
            return $this->xss($field);
        }
        return (string) $this->__get($field);
    }

    /**
     * 
     * @param string $field
     * @return float
     */
    public function float($field)
    {
        return floatval($this->int($field));
    }

    /**
     * 
     * @param string $field
     * @return string
     */
    public function xss($field)
    {
        if (isset($this->_data[$field])) {
            return Xss::filter($this->_data[$field]);
        }
        show_error("'{$key}' doesn't exist in the row object");
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }
}