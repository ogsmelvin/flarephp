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
     * @var \Flare\Db\Sql\Model
     */
    private $_model;

    /**
     * 
     * @param \Flare\Db\Sql\Driver $driver
     * @param \Flare\Db\Sql\Model $model
     */
    public function __construct(Driver &$driver, Model &$model)
    {
        parent::__construct($driver);
        $this->_model = & $model;
        $this->from($this->_model->getTableName());
    }

    /**
     * 
     * @return \Flare\Db\Sql\Result\Collection
     */
    public function getCollection()
    {
        return $this->_getCollection($this->_model);
    }

    /**
     * 
     * @return \Flare\Db\Sql\Model
     */
    public function getOne()
    {
        return $this->_getOne($this->_model);
    }

    /**
     * 
     * @param string $referenceClass
     * @param string $foreignKey
     * @param string $referenceField
     * @return \Flare\Db\Sql\Model\Query
     */
    public function with($referenceClass, $foreignKey = null, $referenceField = null)
    {
        return $this;
    }

    /**
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (strpos($method, '_') === 0) {
            show_error("Can't call private method");
        } elseif (strpos($method, 'with') === 0) {
            array_unshift($args, substr($method, 4));
            $method = 'with';
        }
        return call_user_func_array(array($this, $method), $args);
    }
}