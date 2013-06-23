<?php

namespace FPHP\Db\Sql\Results;

use FPHP\UI\Pagination;
use FPHP\Objects\Json;
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
     * @var \FPHP\UI\Pagination
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
     * @param \FPHP\UI\Pagination $pagination
     * @return \FPHP\Db\Sql\Results\Collection
     */
    public function setPagination(Pagination $pagination)
    {
        $this->_pagination = $pagination;
        return $this;
    }

    /**
     * 
     * @return \FPHP\UI\Pagination
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
            $array[] = $value->toArray();
        }
        return json_encode($array);
    }

    /**
     * 
     * @return \FPHP\Objects\JSON
     */
    public function toJSONObject()
    {
        $array = array();
        foreach($this as $key => $value){
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
        foreach($this as $key => $value){
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
     * @return \FPHP\Objects\Xml
     */
    public function toXMLObject()
    {
        //TODO
    }

    /**
     * 
     * @param string $tag
     * @param string $name
     * @return \FPHP\UI\Html\Element
     */
    public function toHtml($tag, $name)
    {
        $tag = ucwords(strtolower($tag));
        $tag = "\\FPHP\\UI\\Html\\{$tag}";
        return new $tag($name, $this);
    }

    /**
     * 
     * @param callable $callback
     * @return void
     */
    public function each($callback)
    {
        if(is_callable($callback)){
            foreach($this as $key => &$row){
                $callback($key, $row);
            }
        } else {
            display_error(500, "param must be callable");
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