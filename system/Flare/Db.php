<?php

namespace Flare;

/**
 * 
 * @author anthony
 * 
 */
class Db
{
    /**
     *
     * @param string $driver
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string $host
     * @param array $options
     * @return \PDO
     */
    public static function connect($driver, $username, $password, $dbname, $host = 'localhost', $options = array())
    {
        $driver = strtolower($driver);
        $dns = $driver.':host='.$host.';dbname='.$dbname;
        $pdo = "\\Flare\\Db\\Sql\\Driver\\".ucwords($driver);
        return new $pdo($dns, $username, $password, $options);
    }
}