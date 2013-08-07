<?php

namespace Demo\Models;

use Flare\Application\Db\Sql\Model;

class Users extends Model
{
	protected static $table = 'users';
	protected static $primaryKey = 'user_id';
	protected static $alias = 'u';
}