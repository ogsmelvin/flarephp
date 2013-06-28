<?php

namespace Flare\Db\Sql\Results;

use Flare\View\Pagination;
use Flare\Objects\Json;
use \ArrayObject;
use \PDO;

/**
 *
 * @author anthony
 *
 */
class Collection extends ArrayObject
{
    /**
     *
     * @var \PDO
     */
    private $_conn;

    /**
     * 
     * @var int
     */
    private $_dbRowCount;

    /**
     * 
     * @var \Flare\View\Pagination
     */
    private $_pagination = null;

    /**
     *
     * @param \PDO $conn
     * @param int $count
     * @param array $rows
     */
    public function __construct(PDO &$conn, $count = 0, $rows = array())
    {
        $this->_conn = & $conn;
        $this->_dbRowCount = $count;
        parent::__construct($rows);
    }

    /**
     * 
     * @param \Flare\View\Pagination $pagination
     * @return \Flare\Db\Sql\Results\Collection
     */
    public function setPagination(Pagination $pagination)
    {
        $this->_pagination = $pagination;
        return $this;
    }

    /**
     * 
     * @return \Flare\View\Pagination
     */
    public function getPagination()
    {
        return $this->_pagination;
    }

    /**
     * 
     * @return int
     */
    public function rowCount()
    {
        return $this->_dbRowCount;
    }
    
    /**
     * 
     * @return mixed
     */
    public function last()
    {
        return end($this);
    }
    
    /**
     * 
     * @return mixed
     */
    public function first()
    {
        reset($this);
        return current($this);
    }

    /**
     * 
     * @return string
     */
    public function toJSON()
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[] = $value->toArray();
        }
        return json_encode($array);
    }

    /**
     * 
     * @return \Flare\Objects\JSON
     */
    public function toJSONObject()
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[] = $value->toArray();
        }
        return new Json($array);
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $key => $value) {
            $array[] = $value->toArray();
        }
        return $array;
    }

    /**
     * 
     * @return string
     */
    public function toXML()
    {
        //TODO
    }

    /**
     * 
     * @return \Flare\Objects\Xml
     */
    public function toXMLObject()
    {
        //TODO
    }

    /**
     * 
     * @param callable $callback
     * @return void
     */
    public function each($callback)
    {
        if (is_callable($callback)) {
            foreach ($this as $key => &$row) {
                $callback($key, $row);
            }
        } else {
            show_response(500, "param must be callable");
        }
    }

    /**
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->count() ? false : true;
    }
}