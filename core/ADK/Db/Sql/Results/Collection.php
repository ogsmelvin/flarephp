<?php

namespace ADK\Db\Sql\Results;

use ADK\UI\Pagination;
use ADK\Objects\Json;
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
     * @var \ADK\UI\Pagination
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
     * @param \ADK\UI\Pagination $pagination
     * @return \ADK\Db\Sql\Results\Collection
     */
    public function setPagination(Pagination $pagination)
    {
        $this->_pagination = $pagination;
        return $this;
    }

    /**
     * 
     * @return \ADK\UI\Pagination
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
        foreach($this as $key => $value){
            $array[] = (array) $value;
        }
        return json_encode($array);
    }

    /**
     * 
     * @return \ADK\Objects\JSON
     */
    public function toJSONObject()
    {
        $array = array();
        foreach($this as $key => $value){
            $array[] = (array) $value;
        }
        return new JSON($array);
    }

    /**
     * 
     * @param string $tag
     * @param string $name
     * @return \ADK\UI\Html\Element
     */
    public function toHtml($tag, $name)
    {
        $tag = ucwords(strtolower($tag));
        $tag = "\\ADK\\UI\\Html\\{$tag}";
        return new $tag($name, $this);
    }
}