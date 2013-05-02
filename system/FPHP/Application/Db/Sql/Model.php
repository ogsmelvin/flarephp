<?php

namespace FPHP\Application\Db\Sql;

use FPHP\Application\Model as ParentModel;
use FPHP\Db\Sql\Query\ARQuery;
use FPHP\Fphp as A;
use \Exception;

/**
 * 
 * @author anthony
 * 
 */
class Model extends ParentModel
{
    /**
     * 
     * @var string
     */
    protected static $table;

    /**
     * 
     * @var string
     */
    protected static $primaryKey;

    /**
     * 
     * @var PDO
     */
    protected $_adapter;

    /**
     * 
     * @var array
     */
    private static $_instances = array();

    /**
     * 
     * @var string
     */
    protected static $alias;

    protected function __construct()
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
        if(empty(static::$primaryKey)){
            static::$primaryKey = $this->_adapter->getPrimaryKey(static::$table);
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
     * @return \FPHP\Db\Sql\Query\Query
     */
    public function sql($query = null, $bindings = null)
    {
        return $this->_adapter->sql($query, $bindings);
    }

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \FPHP\Db\Sql\Query\ARQuery
     */
    public function insert($data = array(), $check_columns = true)
    {
        return $this->_adapter->insert(static::$table, $data, $check_columns);
    }

    /**
     * 
     * @param string|array $select
     * @return \FPHP\Db\Sql\Query\ARQuery
     */
    public function select($select = '*')
    {
        $sql = $this->_adapter->select($select);
        if(isset(static::$alias)){
            $sql->from(array(static::$alias => static::$table));
        } else {
            $sql->from(static::$table);
        }
        return $sql;
    }

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \FPHP\Db\Sql\Query\ARQuery
     */
    public function update($data = array(), $check_columns = true)
    {
        return $this->_adapter->update(static::$table, $data, $check_columns);
    }

    /**
     * 
     * @return \FPHP\Db\Sql\Query\ARQuery
     */
    public function delete()
    {
        return $this->_adapter->delete(static::$table);
    }

    /**
     * 
     * @return \FPHP\Application\Db\Sql\Model
     */
    public static function query()
    {
        if(empty(static::$table)){
            throw new Exception('Table must be set');
        } else if(!isset(self::$_instances[static::$table])){
            self::$_instances[static::$table] = new static;
        }
        return self::$_instances[static::$table];
    }

    /**
     * 
     * @return string
     */
    public static function getPrimaryKey()
    {
        if(isset(static::$primaryKey)){
            return static::$primaryKey;
        }
        return self::query()->getPrimaryKey(static::$table);
    }

    /**
     * 
     * @param int $limit
     * @param int $page
     * @return \FPHP\Db\Sql\Results\Collection
     */
    public static function getAll($limit = null, $page = null)
    {
        $sql = self::query()->select()
            ->from(static::$table);
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
    public static function find($value, $column = null)
    {
        $sql = self::query()->select()
            ->from(static::$table);
        if(!$column){
            $column = self::getPrimaryKey();
        }
        return $sql->where($column, $value)->getOne();
    }
}