<?php

namespace Flare\Application;

use Flare\Db as Database;
use Flare\Flare as F;
use \PDO;

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
	 * @return \Flare\Db\Sql\Driver
	 */
	public static function getConnection($name, $config = array())
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

	/**
	 * 
	 * @param string $name
	 * @return void
	 */
	public static function disconnect($name = null)
	{
		if (!$name) {
			foreach (self::$_connections as &$conn) {
				if (!$conn->getAttribute(PDO::ATTR_PERSISTENT)) $conn = null;
			}
		} elseif (isset(self::$_connections[$name]) 
			&& !self::$_connections[$name]->getAttribute(PDO::ATTR_PERSISTENT))
		{
			self::$_connections[$name] = null;
		}
	}
}