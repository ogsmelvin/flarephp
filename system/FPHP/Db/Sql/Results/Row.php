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