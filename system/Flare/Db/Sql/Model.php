<?php

namespace Flare\Db\Sql;

use Flare\Db\Model as ParentModel;
use Flare\Db\Sql\Model\Relation;
use Flare\Db\Sql\Model\Query;
use Flare\Security\Xss;

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
     * @var string
     */
    private $namespace;

    /**
     * 
     * @var array
     */
    protected $foreignKeys = array();

    /**
     * 
     * @var array
     */
    protected $attributes = array();

    /**
     * 
     * @var string
     */
    protected $alias;

    /**
     * 
     * @var array
     */
    protected $fields = array();

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
    protected static $metaCache = array();

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (!self::$adapter) {
            self::$adapter = self::_getController()->getDatabase();
            if (!self::$adapter) {
                show_error("Doesn't have database connection");
            }
        }
        $this->_init($data);
    }

    /**
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * 
     * @return string
     */
    protected function getAlias()
    {
        return $this->alias;
    }

    /**
     * 
     * @param string $name
     * @param boolean $withQuote
     * @return string
     */
    protected function getFieldAlias($name, $withQuote = false)
    {
        $quote = $withQuote ? self::$adapter->getQuote() : '';
        if (!$this->alias) {
            return $quote.$this->table.$quote.'.'.$quote.$name.$quote;
        }
        return $quote.$this->alias.$quote.'.'.$quote.$name.$quote;
    }

    /**
     * 
     * @param array $data
     * @return void
     */
    private function _init(array $data)
    {
        if (empty($this->table)) {
            show_error(get_class($this)." table must be defined");
        }

        $this->class = get_class($this);
        $namespace = explode("\\", $this->class);
        array_pop($namespace);
        $this->namespace = implode("\\", $namespace)."\\";
       
        if (!isset(self::$metaCache[$this->class])) {
            self::$metaCache[$this->class] = array();
            if (!empty($this->primaryKey)) {
                self::$metaCache[$this->class]['primary_key'] = $this->primaryKey;
            } else {
                self::$metaCache[$this->class]['primary_key'] = self::$adapter->getPrimaryKey($this->table);
            }
            self::$metaCache[$this->class]['foreign_keys'] = $this->foreignKeys;
        }
        
        if ($data) {
            $this->setAttributes($data);
        }
    }

    /**
     * 
     * @param string $field
     * @param string|array
     */
    public function xss($field)
    {
        return Xss::filter($this->getAttribute($field));
    }

    /**
     * 
     * @param string $field
     * @return int
     */
    public function int($field)
    {
        return intval($this->getAttribute($field));
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
        return (string) $this->getAttribute($field);
    }

    /**
     * 
     * @param string $field
     * @param string $format
     * @return string
     */
    public function date($field, $format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime($this->getAttribute($field)));
    }

    /**
     * 
     * @param string $field
     * @param int $decimals
     * @param string $dec_sep
     * @param string $thousand_sep
     * @return string
     */
    public function number($field, $decimals = 0, $dec_sep = '.', $thousand_sep = ',')
    {
        return number_format($this->getAttribute($field), $decimals, $dec_sep, $thousand_sep);
    }

    /**
     * 
     * @param string $field
     * @return float
     */
    public function float($field)
    {
        return floatval($this->getAttribute($field));
    }

    /**
     * 
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * 
     * @return string
     */
    protected function getTableName()
    {
        return $this->table;
    }

    /**
     * 
     * @return string
     */
    protected function getForeignKeys()
    {
        return self::$metaCache[$this->class]['foreign_keys'];
    }

    /**
     * 
     * @return string
     */
    protected function getPrimaryKey()
    {
        if (!isset(self::$metaCache[$this->class]['primary_key'])) {
            return null;
        }
        return self::$metaCache[$this->class]['primary_key'];
    }

    /**
     * 
     * @return string|int
     */
    public function save()
    {
        return self::$adapter->insert($this->table, $this->attributes, false);
    }

    /**
     * 
     * @return int
     */
    public function remove()
    {
        return self::$adapter->delete($this->table);
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
     * @param string|int $val
     * @param string $field
     * @return \Flare\Db\Sql\Model
     */
    public static function findOne($val, $field = null)
    {
        if (!$field) {
            $field = self::primaryKey();
        }

        $sql = with(new static)->query();
        return $sql->where($field, $value)
            ->getOne();
    }

    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return \Flare\Db\Sql\Model
     */
    public function setAttribute($key, $value)
    {
        if (!$this->fields || in_array($key, $this->fields)) {
            $this->attributes[$key] = $value;
            if (!empty($this->foreignKeys[$key])) {

            }
        }
        return $this;
    }

    /**
     * 
     * @param array $attributes
     * @return \Flare\Db\Sql\Model
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $field => $attr) {
            $this->setAttribute($field, $attr);
        }
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
            if (isset($this->foreignKeys[$key])) {

            }
            show_error("No attribute '{$key}'");
        }
        return $this->attributes[$key];
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * 
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->attributes);
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
     * @return \Flare\Db\Sql\Driver
     */
    public function & getAdapter()
    {
        return self::$adapter;
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
     * @return \Flare\Db\Sql\Model\Query
     */
    public function query()
    {
        return new Query($this);
    }



    /**
     * 
     * @return \Flare\Db\Sql\Model\Query
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
        if (strpos($method, '_') === 0) {
            show_error("Can't access private method");
        } elseif (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }
        return call_user_func_array(array($this->query(), $method), $args);
    }

    /**
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (strpos($method, '_') === 0) {
            show_error("Can't call private method");
        }
        return call_user_func_array(array(new static, $method), $args);
    }
}