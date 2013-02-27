<?php

namespace ADK\Db\Sql\Results;

use ADK\Db\Sql\Query\ARQuery;
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
     * @var \ADK\Db\Sql\Query\ARQuery
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
     * @var boolean
     */
    private $_removed = false;

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
     * @param \ADK\Db\Sql\Query\ARQuery $query
     * @param string $table
     * @param string|int $id
     */
    public function __construct(ARQuery &$query, $table, $pk, $id)
    {
        $this->_query = & $query;
        $this->_table = $table;
        $this->_id = $id;
        $this->_pk = $pk;
    }

    /**
     * 
     * 
     * @return string|int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if(!isset($this->_data[$key])){
            return null;
        }
        return $this->_data[$key];
    }

    /**
     * 
     * @param array $data
     * @return int
     */
    public function save($data)
    {
        if($this->_removed){
            throw new Exception("Row has already been removed");
        }
        $this->_query->clear();
        $affected = $this->_query->update($this->_table, $data)
            ->where($this->_pk, $this->_id)
            ->execute();
        if($affected === 1){
            return true;
        } else if($affected === 0){
            return false;
        } else {
            throw new Exception("Multirows are affected");
        }
    }

    /**
     * 
     * @return boolean
     */
    public function remove()
    {
        if($this->_removed){
            throw new Exception("Row has already been removed");
        }
        $this->_query->clear();
        $affected = $this->_query->delete()
            ->from($this->_table)
            ->where($this->_pk, $this->_id)
            ->execute();
        if($affected === 1){
            return true;
        } else if($affected === 0){
            return false;
        } else {
            throw new Exception("Multirows are affected");
        }
    }
}