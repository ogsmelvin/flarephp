<?php

namespace ADK\Application\Db\Sql;

use ADK\Application\Db\Sql\AbstractTable;
use ADK\Db\Sql\Query\ARQuery;

/**
 * 
 * @author anthony
 * 
 */
class Table extends AbstractTable
{
    /**
     * 
     * @param string $query
     * @param array $bindings
     * @return \ADK\Db\Sql\Query\Query
     */
    public function sql($query = null, $bindings = null)
    {
        return $this->_adapter->sql($query, $bindings);
    }

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function insert($data = array(), $check_columns = true)
    {
        return $this->_adapter->insert($this->_table, $data, $check_columns);
    }

    /**
     * 
     * @param string|array $select
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function select($select = '*')
    {
        $sql = $this->_adapter->select($select);
        if(isset($this->_alias)){
            $sql->from(array($this->_alias => $this->_table));
        } else {
            $sql->from($this->_table);
        }
        return $sql;
    }

    /**
     * 
     * @param array $data
     * @param boolean $check_columns
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function update($data = array(), $check_columns = true)
    {
        return $this->_adapter->update($this->_table, $data, $check_columns);
    }

    /**
     * 
     * @return \ADK\Db\Sql\Query\ARQuery
     */
    public function delete()
    {
        return $this->_adapter->delete($this->_table);
    }
}