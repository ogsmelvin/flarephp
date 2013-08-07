<?php

namespace Flare\Cache;

use Flare\Cache;

/**
 * 
 * @author anthony
 * 
 */
class Memcache extends Cache
{
	/**
	 * 
	 * @var string
	 */
	protected static $engine = __CLASS__;

	/**
	 * 
	 * @param array $params
	 * @return void
	 */
	protected function init(array $params)
	{

	}

	/**
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{

	}

	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return \Flare\Cache
	 */
	public function set($key, $value, $expiration = 0)
	{
		return $this;
	}
}