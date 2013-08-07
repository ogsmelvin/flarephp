<?php

namespace Flare\Util;

/**
 * 
 * @author anthony
 * 
 */
class String
{
	/**
	 * 
	 * @param string 
	 */
	public function __construct($string)
	{
		$this->_string = (string) $string;
	}

	/**
	 * 
	 * @param string|array $value
	 * @return string|array
	 */
	public static function stripSlashes($value)
	{
		if (is_array($value)) {
			foreach ($value as $key => $val) {
				$value[$key] = self::stripSlashes($value[$key]);
			}
		} else {
			$value = stripslashes($value);
		}
		return $value;
	}
}