<?php

namespace ADK\Db\Sql;

/**
 * 
 * @author anthony
 * @abstract
 */
interface Sql
{
    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return void
     */
    public function sql($query = null, $bindings = null);

    /**
     * 
     * @param string $table
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function insert($table, $data = array(), $check_columns = true);

    /**
     * 
     * @param string|array $select
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function select($select);

    /**
     * 
     * @param string $table
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function update($table, $data = array(), $check_columns = true);

    /**
     * 
     * @param string $table
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function delete($table);

    /**
     * 
     * @param string $table
     * @return string
     */
    public function getPrimaryKey($table);
}