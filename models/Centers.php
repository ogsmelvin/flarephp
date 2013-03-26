<?php

namespace Models;

use ADK\Application\Db\Sql\Table;

class Centers extends Table
{
    protected $_table = 'voting_centers';
    protected $_primaryKey = 'voting_center_id';
    protected $_alias = 'vc';
}