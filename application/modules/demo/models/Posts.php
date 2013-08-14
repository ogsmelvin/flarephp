<?php

namespace Demo\Models;

use Flare\Application\Db\Sql\Model;

class Posts extends Model
{
    protected static $table = 'posts';
    protected static $primaryKey = 'post_id';
    protected static $alias = 'p';
}