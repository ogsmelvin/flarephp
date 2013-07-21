<?php

namespace Demo\Models;

use Flare\Application\Db\Sql\Model;

class Registrants extends Model
{
    protected static $table = 'registrants';
    protected static $primaryKey = 'registrant_id';
    protected static $alias = 'r';
}