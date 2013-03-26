<?php

namespace Models;

use ADK\Application\Db\Sql\Table;

class Users extends Table
{
    protected $_table = 'users';
    protected $_primaryKey = 'user_id';
    protected $_alias = 'u';

    public function getUsers()
    {
        return $this->select()
            ->getArray();
    }
}