<?php

namespace ADK\Application\Db\Sql;

use ADK\Adk as A;
use \Exception;

/**
 * 
 * @author anthony
 * @abstract
 * 
 */
abstract class AbstractTable
{
    /**
     * 
     * @var PDO
     */
    protected $_adapter;

    /**
     * 
     * @var string
     */
    protected $_table;

    /**
     * 
     * @var string
     */
    protected $_alias;

    /**
     * 
     * @var string
     */
    protected $_primaryKey;

    public function __construct()
    {
        $this->_setup();
    }

    /**
     * 
     * @return void
     */
    protected function _setup()
    {
        $this->_adapter = & A::db();
        if(!isset($this->_table)){
            throw new Exception('Table must be set');
        }
        if(!isset($this->_primaryKey)){
            $this->_primaryKey = $this->_adapter->getPrimaryKey($table);
        }
    }

    /**
     * 
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return void
     */
    abstract public function sql($query = null, $bindings = null);

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    abstract public function insert($data = array(), $check_columns = true);

    /**
     * 
     * @param string|array $select
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    abstract public function select($select);

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    abstract public function update($data = array(), $check_columns = true);

    /**
     * 
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    abstract public function delete();

    /**
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        if(isset($this->_primaryKey)){
            return $this->_primaryKey;
        }
        return $this->_adapter->getPrimaryKey($this->_table);
    }

    /**
     * 
     * @param int $limit
     * @param int $page
     * @return \ADK\Db\Sql\Results\Collection
     */
    public function getAll($limit = null, $page = null)
    {
        $sql = $this->select()
            ->from($this->_table);
        if($limit){
            $sql->limit($limit);
        }
        if($page){
            $sql->page($page);
        }
        return $sql->getCollection();
    }

    /**
     * 
     * @param string|int $value
     * @param strign $column
     * @return stdClass
     */
    public function find($value, $column = null)
    {
        $sql = $this->select()
            ->from($this->_table);
        if(!$column){
            $column = $this->getPrimaryKey();
        }
        return $sql->where($column, $value)->getOne();
    }
}