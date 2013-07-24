<?php

namespace Flare\Application;

use Flare\Db as Database;
use Flare\Flare as F;

/**
 * 
 * @author anthony
 * 
 */
class Db
{
    /**
     * 
     * @var array
     */
    private static $_connections = array();

    /**
     * 
     * @param string $name
     * @param array $config
     * @return \PDO
     */
    public static function & getConnection($name, $config = array())
    {
        if (!isset(self::$_connections[$name])) {
            if (!$config) {
                if (!isset(F::$config->database[$name])) {
                    show_error("No database configuration found");
                }
                $config = F::$config->database[$name];
            }
            self::$_connections[$name] = Database::connect(
                $config['driver'],
                $config['username'],
                $config['password'],
                $config['dbname'],
                $config['host'],
                $config['options']
            );
        }
        return self::$_connections[$name];
    }
}