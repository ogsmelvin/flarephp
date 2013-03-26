<?php

namespace Models;

use ADK\Application\Db\Sql\Table;

class Senders extends Table
{
    protected $_table = 'vote_senders';
    protected $_primaryKey = 'sender_id';
    protected $_alias = 'vs';
}