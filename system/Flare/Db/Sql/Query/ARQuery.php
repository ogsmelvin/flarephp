<?php

namespace Flare\Db\Sql\Query;

use Flare\Db\Sql\Results\Collection;
use Flare\Db\Sql\Results\Row;
use Flare\View\Pagination;
use Flare\Objects\Json;
use \PDOException;
use \PDOStatement;
use \Exception;
use \PDO;

/**
 *
 * @author anthony
 *
 */
class ARQuery
{
    /**
     *
     * @var boolean
     */
    private $_distinct = false;

    /**
     * 
     * @var string
     */
    private $_table = null;

    /**
     * 
     * @var boolean
     */
    private $_checkTable = false;

    /**
     *
     * @var \PDO
     */
    private $_conn;

    /**
     *
     * @var string
     */
    private $_select = null;

    /**
     *
     * @var string
     */
    private $_from = null;

    /**
     *
     * @var array
     */
    private $_where = array();

    /**
     *
     * @var array
     */
    private $_joins = array();

    /**
     *
     * @var array
     */
    private $_like = array();

    /**
     *
     * @var int
     */
    private $_limit = null;

    /**
     *
     * @var int
     */
    private $_offset = null;

    /**
     *
     * @var array
     */
    private $_groups = array();

    /**
     *
     * @var array
     */
    private $_orders = array();

    /**
     *
     * @var boolean
     */
    private $_delete = false;

    /**
     *
     * @var string
     */
    private $_insert = null;

    /**
     *
     * @var string
     */
    private $_update = null;

    /**
     *
     * @var array
     */
    private $_set = array();

    /**
     *
     * @var int
     */
    private $_page = false;

    /**
     *
     * @var array
     */
    private $_having = array();

    /**
     *
     * @param \PDO $conn
     */
    public function __construct(PDO &$conn)
    {
        $this->_conn = & $conn;
    }

    /**
     *
     * @param string|array $fields
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function select()
    {
        $args = func_get_args();
        if (func_num_args() > 1) {
            foreach (func_get_args() as $field) {
                $fields[] = $field;
            }
            $this->_select = implode(',', $fields);
        } elseif (is_array($args[0])) {
            foreach ($args[0] as $alias => &$name) {
                if (is_int($alias)) {
                    $alias = $name;
                }
                $name = $this->_conn->quoteAs($name, $alias);
            }
            $this->_select = implode(',', $args[0]);
        } elseif (is_string($args[0])) {
            $this->_select = $args[0];
        }
        return $this;
    }

    /**
     *
     * @param string $field
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function groupBy($field)
    {
        $this->_groups[] = $this->_conn->quoteColumn($field);
        return $this;
    }

    /**
     *
     * @param string $field
     * @param string $order
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orderBy($field, $order = 'ASC')
    {
        $this->_orders[] = $this->_conn->quoteColumn($field)." {$order}";
        return $this;
    }

    /**
     *
     * @param array|string $tables
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function from($tables)
    {
        if (is_array($tables)) {
            foreach ($tables as $alias => &$name) {
                $name = $this->_conn->quoteAs($name, $alias);
            }
            $this->_from = implode(',', $tables);
        } elseif (is_string($tables)) {
            $this->_from = $tables;
        }
        return $this;
    }

    /**
     *
     * @param string $field
     * @param string|int|array $value
     * @param string $comparison
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    protected function _where($field, $value, $comparison, $condition)
    {
        $type = PDO::PARAM_STR;
        if (is_float($value) || is_int($value)) {
            $type = PDO::PARAM_INT;
        }
        if ($value instanceof Json || $value instanceof Xml) {
            $value = (array) $value;
        }
        if (is_array($value)) {
            foreach ($value as &$val) {
                $type = PDO::PARAM_STR;
                if (is_float($value) || is_int($val)) {
                    $type = PDO::PARAM_INT;
                }
                $val = $this->_conn->quote($val, $type);
            }
            $this->_where[$condition][] = $this->_conn->quoteColumn($field)
                ." {$comparison} "
                ."(".implode(',', $value).")";
        } else {
            $this->_where[$condition][] = $this->_conn->quoteColumn($field)
                ." {$comparison} "
                .$this->_conn->quote($value, $type);
        }
        return $this;
    }

    /**
     * 
     * //TODO
     * @param string $field
     * @param string|int $value
     * @param string $comparison
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    protected function _having($field, $value, $comparison, $condition)
    {
        $this->_having[] = array();
        return $this;
    }

    /**
     * 
     * //TODO
     * @param string $field
     * @param string $value
     * @param string $comparison
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function having($field, $value, $comparison = '=')
    {
        return $this->_having($field, $value, $comparison, 'AND');
    }

    /**
     * //TODO
     * @param string $field
     * @param string $value
     * @param string $comparison
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orHaving($field, $value, $comparison = '=')
    {
        return $this->_having($field, $value, $comparison, 'OR');
    }

    /**
     *
     * @param string $field
     * @param string|int $value
     * @param string $comparison
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function where($field, $value, $comparison = '=')
    {
        return $this->_where($field, $value, $comparison, 'AND');
    }

    /**
     *
     * @param string $field
     * @param string|int $value
     * @param string $comparison
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orWhere($field, $value, $comparison = '=')
    {
        return $this->_where($field, $value, $comparison, 'OR');
    }

    /**
     *
     * @param string $field
     * @param array $values
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function whereIn($field, $values)
    {
        return $this->_where($field, $values, 'IN', 'AND');
    }

    /**
     *
     * @param string $field
     * @param array $values
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orWhereIn($field, $values)
    {
        return $this->_where($field, $values, 'IN', 'OR');
    }

    /**
     *
     * @param string $field
     * @param array $values
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orWhereNotIn($field, $values)
    {
        return $this->_where($field, $values, 'NOT IN', 'OR');
    }

    /**
     *
     * @param string $field
     * @param array $values
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function whereNotIn($field, $values)
    {
        return $this->_where($field, $values, 'NOT IN', 'AND');
    }

    /**
     *
     * @return string
     */
    protected function _compile()
    {
        $sql = '';
        $hasWhere = false;
        $hasAndOr = false;
        $quote = $this->_conn->getQuote();
        if ($this->_select) {
            $sql .= 'SELECT ';
            if ($this->_distinct) {
                $sql .= 'DISTINCT ';
            }
            $sql .= $this->_select;
        } elseif ($this->_delete) {
            $sql .= 'DELETE';
        } elseif ($this->_update) {
            $sql .= $this->_update.' ';
            if ($this->_set) {
                $sql .= 'SET ';
                foreach ($this->_set as $k => $v) {
                    $sql .= "{$quote}{$k}{$quote} = {$v}, ";
                }
                $sql = trim($sql, ', ').' ';
            }
        } elseif ($this->_insert) {
            $sql .= $this->_insert;
            if ($this->_set) {
                $fields = array_keys($this->_set);
                $sql .= "({$quote}".implode("{$quote},{$quote}", $fields)."{$quote}) VALUES(";
                $sql .= implode(',', $this->_set).')';
                unset($fields);
            }
        }
        if ($this->_from) {
            $sql .= ' FROM '.$this->_from;
        }
        if ($this->_joins) {
            $sql .= ' '.implode(' ', $this->_joins);
        }
        if ($this->_where) {
            $sql .= ' WHERE ';
            $hasWhere = true;
            foreach ($this->_where as $condition => $conds) {
                if (!$hasAndOr) {
                    $hasAndOr = true;
                } else {
                    $sql .= $condition.' ';
                }
                $sql .= implode(" {$condition} ", $conds).' ';
            }
        }
        if ($this->_like) {
            if (!$hasWhere) {
                $sql .= ' WHERE ';
                $hasWhere = true;
            }
            foreach ($this->_like as $condition => $conds) {
                if (!$hasAndOr) {
                    $hasAndOr = true;
                } else {
                    $sql .= $condition.' ';
                }
                $sql .= implode(" {$condition} ", $conds).' ';
            }
        }
        if ($this->_groups) {
            $sql .= ' GROUP BY '.implode(', ', $this->_groups);
        }
        if ($this->_having) {

        }
        if ($this->_orders) {
            $sql .= ' ORDER BY '.implode(', ', $this->_orders);
        }
        if ($this->_limit) {
            $sql .= ' LIMIT '.$this->_limit;
        }
        if ($this->_offset) {
            $sql .= ' OFFSET '.$this->_offset;
        }
        return trim($sql);
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @param string $comparison
     * @param string $condition
     * @param string $place
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    protected function _like($field, $value, $comparison, $condition, $place = null)
    {
        if ($place) {
            if ($place === 'BEFORE') {
                $value = '%'.$value;
            } elseif ($place === 'AFTER') {
                $value = $value.'%';
            }
        } else {
            $value = '%'.$value.'%';
        }
        $this->_like[$condition][] = $this->_conn->quoteColumn($field).
            " {$comparison} ".
            $this->_conn->quote($value);
        return $this;
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function like($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'AND');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function likeBefore($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'AND', 'BEFORE');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function likeAfter($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'AND', 'AFTER');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function notLike($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'AND');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function notLikeBefore($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'AND', 'BEFORE');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function notLikeAfter($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'AND', 'AFTER');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orNotLike($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'OR');
    }

    /**
     *
     * @param type $field
     * @param type $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orNotLikeBefore($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'OR', 'BEFORE');
    }

    /**
     *
     * @param type $field
     * @param type $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orNotLikeAfter($field, $value)
    {
        return $this->_like($field, $value, 'NOT LIKE', 'OR', 'AFTER');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orLike($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'OR');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orLikeBefore($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'OR', 'BEFORE');
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function orLikeAfter($field, $value)
    {
        return $this->_like($field, $value, 'LIKE', 'OR', 'AFTER');
    }

    /**
     *
     * @param int $limit
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function limit($limit)
    {
        if ($limit) {
            $this->_limit = (int) $limit;
        }
        return $this;
    }

    /**
     *
     * @param int $offset
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function offset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }

    /**
     *
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function count($field = '*')
    {
        return $this->select('COUNT('.$this->_conn->quoteColumn($field).') AS `total`');
    }

    /**
     *
     * @return \Flare\Db\Sql\Results\Collection
     */
    public function getCollection()
    {
        $result = null;
        try {
            $pagination = null;
            if ($this->_page) {
                $pagination = $this->_paginate();
            }
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            $result = new Collection($this->_conn, $stmt->rowCount());
            $newRow = new Row($this);
            if ($pagination) {
                $result->setPagination($pagination);
            }
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = clone $newRow->setData($row);
            }
            unset($newRow);
            $stmt = null;
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $result;
    }

    /**
     *
     * @return \Flare\View\Pagination
     */
    protected function _paginate()
    {
        $limit = $this->_limit;
        $offset = $this->_offset;
        $select = $this->_select;
        $this->count()
            ->limit(null)
            ->offset(0);
        $count = $this->getOneAsArray();
        if (isset($count['total'])) {
            $count = (int) $count['total'];
        }
        $this->select($select)
            ->limit($limit);
        if ($offset) {
            $this->offset($offset);
        } else {
            $this->offset(($this->_page - 1) * (int) $this->_limit);
        }
            
        $pagination = new Pagination();
        return $pagination
            ->setCurrentPage($this->_page)
            ->setTotalCount($count)
            ->setLimitPerPage($this->_limit)
            ->setOffset($this->_offset);
    }

    /**
     *
     * @return stdClass
     */
    public function getOne()
    {
        $result = null;
        try {
            $this->limit(1);
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = new Row($this);
                $result->setData($row);
            }
            $stmt = null;
            unset($row);
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $result;
    }

    /**
     * Clear all
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function clear()
    {
        $this->_distinct = false;
        $this->_select = null;
        $this->_from = null;
        $this->_where = array();
        $this->_joins = array();
        $this->_like = array();
        $this->_limit = null;
        $this->_offset = null;
        $this->_groups = array();
        $this->_orders = array();
        $this->_delete = false;
        $this->_insert = null;
        $this->_update = null;
        $this->_set = array();
        $this->_having = array();
        $this->_page = false;
        $this->_checkTable = false;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getArray()
    {
        $result = null;
        try {
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function get()
    {
        $result = null;
        try {
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $stmt = null;
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $result;
    }

    /**
     *
     * @return array
     */
    public function getOneAsArray()
    {
        $result = null;
        try {
            $this->limit(1);
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = $row;
            }
            $stmt = null;
            unset($row);
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $result;
    }

    /**
     *
     * @return string
     */
    public function getJSON()
    {
        return json_encode($this->getArray());
    }

    /**
     *
     * @return \Lazarus_Json
     */
    public function getJSONObject()
    {
        return new Json($this->getArray());
    }

    /**
     *
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function delete()
    {
        $this->_delete = true;
        return $this;
    }

    /**
     *
     * @param array $set
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function update($table, $set = array(), $check_columns = true)
    {
        $this->_update = 'UPDATE '.$this->_conn->quoteIdentifier($table);
        $this->_table = $table;
        if ($check_columns) {
            $this->_checkTable = true;
        }
        if ($set) {
            foreach ($set as $key => $value) {
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     *
     * @param string $key
     * @param string|int $value
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function set($key, $value)
    {
        $type = PDO::PARAM_STR;
        if (is_float($value) || is_int($value)) {
            $type = PDO::PARAM_INT;
        }
        $this->_set[$key] = $this->_conn->quote($value, $type);
        return $this;
    }

    /**
     * 
     * @param string $table
     * @param array $set
     * @param boolen $check_columns
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function insert($table, $set = array(), $check_columns = true)
    {
        $this->_insert = 'INSERT INTO '.$this->_conn->quoteIdentifier($table);
        $this->_table = $table;
        if ($check_columns) {
            $this->_checkTable = true;
        }
        if ($set) {
            foreach ($set as $key => $value) {
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * 
     * @param string $table
     * @param array $inserts
     * @param boolen $check_columns
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function insertByBatch($table, $inserts, $check_columns = true)
    {
        //TODO
        return $this;
    }

    /**
     *
     * @return int
     */
    public function execute()
    {
        $return = null;
        try {
            if (($this->_insert || $this->_update) && $this->_checkTable) {
                $columns = $this->_conn->getColumns($this->_table, true);
                foreach ($this->_set as $k => $v) {
                    if (!in_array($k, $columns)) {
                        unset($this->_set[$k]);
                    }
                }
            }
            $stmt = $this->_conn->prepare($this->_compile());
            $stmt->execute();
            $this->_conn->printError($stmt);
            if ($this->_insert) {
                $return = $this->lastInsertId();
                if ($return === '0') {
                    $pkField = $this->_conn->getPrimaryKey($this->_table);
                    if (isset($this->_set[$pkField])) {
                        $return = $this->_set[$pkField];
                    } elseif ($stmt->rowCount()) {
                        $return = true;
                    }
                }
            } elseif ($this->_update || $this->_delete) {
                $return = $stmt->rowCount();
            }
            $stmt = null;
        } catch(PDOException $ex) {
            show_error($ex->getMessage());
        }
        return $return;
    }

    /**
     * 
     * @return string
     */
    public function lastInsertId()
    {
        return $this->_conn->lastInsertId();
    }

    /**
     *
     * @return \Flare\Db\Sql\Results\Row
     */
    public function pull()
    {
        $object = null;
        if ($this->_insert && $id = $this->execute()) {
            $table = trim(str_replace('INSERT INTO ', '', $this->_insert), $this->_conn->getQuote());
            $object = new Row($this, $table, $this->_conn->getPrimaryKey($table), $id);
            $object->setData($this->_set);
        } else {
            throw new Exception("Cannot use pull for this query");
        }
        return $object;
    }

    /**
     *
     * @return \PDO
     */
    public function & getConnection()
    {
        return $this->_conn;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_compile();
    }

    /**
     *
     * @param string|array $table
     * @param string $condition
     * @param string $type
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    protected function _join($table, $condition, $type)
    {
        $joinStr = '';
        if (is_string($table)) {
            $joinStr = $type.' '.$table;
        } elseif (is_array($table)) {
            $quote = $this->_conn->getQuote();
            $joinStr = $type.' '.$quote.implode("{$quote} AS {$quote}", $table).$quote;
        }
        $this->_joins[] = $joinStr.' ON '.$condition;
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function join($table, $condition)
    {
        return $this->_join($table, $condition, 'JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinInner($table, $condition)
    {
        return $this->_join($table, $condition, 'INNER JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinLeft($table, $condition)
    {
        return $this->_join($table, $condition, 'LEFT JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinRight($table, $condition)
    {
        return $this->_join($table, $condition, 'RIGHT JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinLeftOuter($table, $condition)
    {
        return $this->_join($table, $condition, 'LEFT OUTER JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinRightOuter($table, $condition)
    {
        return $this->_join($table, $condition, 'RIGHT OUTER JOIN');
    }

    /**
     *
     * @param string $table
     * @param string $condition
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function joinOuter($table, $condition)
    {
        return $this->_join($table, $condition, 'OUTER JOIN');
    }

    /**
     *
     * @param int $page
     * @param int $limit
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function page($page, $limit = null)
    {
        if ($page) {
            $this->_page = (int) $page;
            if (!$this->_page) {
                $this->_page = 1;
            }
        }
        if ($limit) {
            $this->limit($limit);
        }
        // $this->offset(($this->_page - 1) * (int) $this->_limit);
        return $this;
    }

    /**
     *
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    public function distinct()
    {
        $this->_distinct = true;
        return $this;
    }
}