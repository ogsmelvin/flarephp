<?php

namespace Flare\Db\Sql;

use \PDO;

/**
 * 
 * @author anthony
 * 
 */
abstract class Driver extends PDO
{
    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return void
     */
    abstract public function sql($query = null, $bindings = null);

    /**
     * 
     * @param string $table
     * @param array $data
     * @param boolean $check_columns
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    abstract public function insert($table, $data = array(), $check_columns = true);

    /**
     * 
     * @param string|array $select
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    abstract public function select($select);

    /**
     * 
     * @param string $table
     * @param array $data
     * @param boolean $check_columns
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    abstract public function update($table, $data = array(), $check_columns = true);

    /**
     * 
     * @param string $table
     * @return \Flare\Db\Sql\Query\ARQuery
     */
    abstract public function delete($table);

    /**
     * 
     * @param string $table
     * @return string
     */
    abstract public function getPrimaryKey($table);
}
