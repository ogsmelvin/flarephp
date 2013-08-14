<?php

namespace Flare\Db\Sql\Result;

use Flare\Util\Collection as ParentCollection;
use Flare\View\Pagination;
use Flare\Db\Sql\Driver;
use Flare\Object\Json;

/**
 *
 * @author anthony
 *
 */
class Collection extends ParentCollection
{
    /**
     *
     * @var \Flare\Db\Sql\Driver
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
    private $_pagination;

    /**
     *
     * @param \Flare\Db\Sql\Driver $conn
     * @param int $count
     * @param array $rows
     */
    public function __construct(Driver &$conn, $count = 0, $rows = array())
    {
        $this->_conn = & $conn;
        $this->_dbRowCount = $count;
        parent::__construct($rows);
    }

    /**
     * 
     * @param \Flare\View\Pagination $pagination
     * @return \Flare\Db\Sql\Result\Collection
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
     * @return \Flare\Object\JSON
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
     * @return \Flare\Object\Xml
     */
    public function toXMLObject()
    {
        //TODO
    }
}