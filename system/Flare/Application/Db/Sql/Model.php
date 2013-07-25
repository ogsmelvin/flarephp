<?php

namespace Flare\Application\Db\Sql;

use Flare\Application\Model as ParentModel;
use Flare\Application\Registry;
use Flare\Flare as F;

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
        if (self::getController()->getDatabase()) {
            $this->_adapter = self::getController()->getDatabase();
        } else {
            show_error("No database connection");
        }
        if (empty(static::$primaryKey)) {
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
     * @return \Flare\Db\Sql\Query\Query
     */
    public function sql($query = null, $bindings = null)
    {
        return $this->_adapter->sql($query, $bindings);
    }

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function insert($data = array(), $check_columns = true)
    {
        return $this->_adapter->insert(static::$table, $data, $check_columns)->execute();
    }

    /**
     * 
     * @param string|array $select
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function select($select = '*')
    {
        $sql = $this->_adapter->select($select);
        if (isset(static::$alias)) {
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
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function update($data = array(), $check_columns = true)
    {
        return $this->_adapter->update(static::$table, $data, $check_columns)->execute();
    }

    /**
     * 
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function delete()
    {
        return $this->_adapter->delete(static::$table)->execute();
    }

    /**
     * 
     * @return \Flare\Application\Db\Sql\Model
     */
    public static function query()
    {
        if (empty(static::$table)) {
            show_error('Table must be set');
        }
        $registry = Registry::getInstance(Registry::MODELS_NAMESPACE);
        if (!$registry->has(static::$table)) {
            $registry->add(static::$table, new static);
        }
        return $registry->get(static::$table);
    }

    /**
     * 
     * @return string
     */
    public static function getPrimaryKey()
    {
        if (isset(static::$primaryKey)) {
            return static::$primaryKey;
        }
        return self::query()->getAdapter()->getPrimaryKey(static::$table);
    }

    /**
     * 
     * @param int $limit
     * @param int $page
     * @return \Flare\Db\Sql\Result\Collection
     */
    public static function getAll($limit = null, $page = null)
    {
        $sql = self::query()->select()
            ->from(static::$table);
        if ($limit) {
            $sql->limit($limit);
        }
        if ($page) {
            $sql->page($page);
        }
        return $sql->getCollection();
    }

    /**
     * 
     * @param string|int $value
     * @param string $column
     * @return stdClass
     */
    public static function findOne($value, $column = null)
    {
        $sql = self::query()->select()
            ->from(static::$table);
        $pk = self::getPrimaryKey();
        if (!$column) {
            $column = $pk;
        }
        $row = $sql->where($column, $value)->getOne();
        if ($row) {
            $row->setTable(static::$table);
            $row->setPrimaryKey($pk);
            if ($row->{$pk} !== null) {
                $row->setId($row->{$pk});
            }
        }
        return $row;
    }

    /**
     * 
     * @param array $data
     * @return string
     */
    public static function save($data)
    {
        return self::query()->insert($data);
    }

    /**
     * 
     * @param string $field
     * @return string
     */
    public static function alias($field)
    {
        return (isset(static::$alias) ? static::$alias : static::$table).'.'.$field;
    }

    /**
     * 
     * @return string
     */
    public static function getAlias()
    {
        return isset(static::$alias) ? static::$alias : static::$table;
    }

    /**
     * 
     * @return string
     */
    public static function getTableName()
    {
        return static::$table;
    }

    /**
     * 
     * @return string
     */
    public static function getTableAlias()
    {
        return isset(static::$alias) ? static::$table.' AS '.static::$alias : static::$table;
    }

    /**
     * 
     * @return string
     */
    public static function getPrimaryKeyAlias()
    {
        return (isset(static::$alias) ? static::$alias : static::$table).'.'.self::getPrimaryKey();
    }
}