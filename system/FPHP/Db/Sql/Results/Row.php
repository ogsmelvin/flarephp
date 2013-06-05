<?php

namespace FPHP\Db\Sql\Results;

use FPHP\Db\Sql\Query\ARQuery;
use \Exception;

/**
 * 
 * @author anthony
 * 
 */
class Row
{
    /**
     * 
     * @var \FPHP\Db\Sql\Query\ARQuery
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
     * @param \FPHP\Db\Sql\Query\ARQuery $query
     * @param string $table
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
     * @return \FPHP\Db\Sql\Results\Row
     */
    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \FPHP\Db\Sql\Results\Row
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
     * @return \FPHP\Db\Sql\Results\Row
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * 
     * @param array $data
     * @return \FPHP\Db\Sql\Results\Row
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if(!isset($this->_data[$key])){
            display_error("'{$key}' doesn't exist in the row object");
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
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $method = preg_split('/(?=[A-Z])/', $method);
        if(isset($method[0]) && $method[0] === 'get'){
            unset($method[0]);
            return $this->html(strtolower(implode('_', $method)));
        }
        display_error("{$method} doesn't exists");
    }

    /**
     * 
     * @param string $field
     * @return mixed
     */
    public function html($field)
    {
        if(isset($this->_data[$field])){
            return html($this->_data[$field]);
        }
        display_error("'{$key}' doesn't exist in the row object");
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