<?php

namespace Flare\Db\Sql\Model;

use Flare\Db\Sql\Query\ARQuery;
use Flare\Db\Sql\Driver;
use Flare\Db\Sql\Model;

/**
 * 
 * @author anthony
 * 
 */
class Query extends ARQuery
{
    /**
     * 
     * @var string
     */
    private $_model;

    /**
     * 
     * @param \Flare\Db\Sql\Driver $driver
     * @param string $model
     */
    public function __construct(Driver &$driver, $model)
    {
        parent::__construct($driver);
        $this->_model = $model;
    }

    /**
     * 
     * @return \Flare\Db\Sql\Result\Collection
     */
    public function getCollection()
    {
        return $this->_getCollection($this->_model);
    }
}