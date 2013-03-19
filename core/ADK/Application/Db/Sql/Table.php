<?php

namespace ADK\Application\Db\Sql;

use ADK\Application\Db\Sql\AbstractTable;
use ADK\Adk as A;

/**
 * 
 * @author anthony
 * 
 */
class Table extends AbstractTable
{
    /**
     * 
     * @var PDO
     */
    private $_adapter;

    public function __construct()
    {
        $this->_setup();
    }

    protected function _setup()
    {
        $this->_adapter = & A::db();
        if(!isset($this->_table)){

        }
        if(!isset($this->_primaryKey)){
            $this->_primaryKey = $this->_adapter->getPrimaryKey($table);
        }
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }
}