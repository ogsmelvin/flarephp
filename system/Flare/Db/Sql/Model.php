<?php

namespace Flare\Db\Sql;

use Flare\Db\Model as ParentModel;

/**
 * 
 * @author anthony
 * 
 */
abstract class Model extends ParentModel
{
    /**
     * 
     * @var string
     */
    protected $table;

    /**
     * 
     * @var string
     */
    protected $primaryKey;

    /**
     * 
     * @var array
     */
    protected $attributes = array();

    /**
     * 
     * @var \Flare\Db\Sql\Driver
     */
    private static $adapter;

    /**
     * 
     * @var string
     */
    private $class;

    /**
     * 
     * @var array
     */
    private static $metaCache = array();

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (!self::$adapter) {
            self::$adapter = self::getController()->getDatabase();
            if (!self::$adapter) {
                show_error("Doesn't have database connection");
            }
        }
        $this->init($data);
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    private function init(array $data)
    {
        if (empty($this->table)) {
            show_error(get_class($this)." table must be defined");
        }

        $this->class = get_class($this);
        if (!isset(self::$metaCache[$this->class])) {
            self::$metaCache[$this->class] = array();
            if (empty(self::$metaCache[$this->class]['primary_key'])) {
                if (!empty($this->primaryKey)) {
                    self::$metaCache[$this->class]['primary_key'] = $this->primaryKey;
                } else {
                    self::$metaCache[$this->class]['primary_key'] = self::$adapter->getPrimaryKey($this->table);
                }
            }
        }
        
        if ($data) {
            foreach ($data as $key => $value) {
                $this->setAttribute($key, $value);
            }
        }
    }

    /**
     * 
     * @return string
     */
    public static function primaryKey()
    {
        $class = get_called_class();
        if (empty(self::$metaCache[$class]['primary_key'])) {
            return null;
        }
        return self::$metaCache[$class]['primary_key'];
    }

    /**
     * 
     * @param array $data
     * @return \Flare\Db\Sql\Model
     */
    public static function create(array $data)
    {
        return new static($data);
    }

    /**
     * 
     * @param int $limit
     * @param int $page
     * @return \Flare\Db\Sql\Result\Collection
     */
    public static function all($limit = null, $page = null)
    {
        $sql = with(new static)->query();
        if ($limit !== null) {
            $sql->limit($limit);
        }
        if ($page !== null) {
            $sql->page($page);
        }
        return $sql->getCollection();
    }

    /**
     * 
     * @param string $val
     * @param string $field
     * @return \Flare\Db\Sql\Result\Collection
     */
    public static function find($val, $field = null)
    {
        if (!$field) {
            $field = self::primaryKey();
        }

        $sql = with(new static)->query();
        return $sql->where($field, $value)
            ->getCollection();
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \Flare\Db\Sql\Model
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[str_replace(' ', '_', $key)] = $value;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!isset($this->attributes[$key])) {
            show_error("No attribute '{$key}'");
        }
        return $this->attributes[$key];
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * 
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * 
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function query()
    {
        return self::$adapter->from($this->table);
    }

    /**
     * 
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public static function createQuery()
    {
        return with(new static)->query();
    }

    /**
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }
        
        $arQuery = $this->query();
        if (!method_exists($arQuery, $method)) {
            show_error("'{$method}' doesn't exists");   
        }
        return call_user_func_array(array($arQuery, $method), $args);
    }

    /**
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(new static, $method), $args);
    }
}