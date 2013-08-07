<?php

namespace Flare\Security;

if (!function_exists('mcrypt_encrypt')) {
	show_error('Flare\Security\Crypt requires mcrypt library');
}

use Flare\Security;

/**
 * 
 * @author anthony
 * 
 */
class Crypt extends Security
{
	/**
	 * 
	 * @param string $str
	 * @param string $key
	 * @param string $cipher
	 * @param string $mode
	 * @return string
	 */
	public static function encode($str, $key, $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
	{
		$result = null;
		$iv_size = mcrypt_get_iv_size($cipher, $mode);
		if ($iv_size) {
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$result = mcrypt_encrypt($cipher, $key, $str, $mode, $iv);
			$result = base64_encode($iv.$result);
		}

		return $result;
	}

	/**
	 * 
	 * @param string $str
	 * @param string $key
	 * @param string $cipher
	 * @param string $mode
	 * @return string
	 */
	public static function decode($str, $key, $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
	{
		$result = null;
		$iv_size = mcrypt_get_iv_size($cipher, $mode);
		if ($iv_size) {
			$str = base64_decode($str);
			$iv_dec = substr($str, 0, $iv_size);
			$str = substr($str, $iv_size);
			$result = mcrypt_decrypt($cipher, $key, $str, $mode, $iv_dec);
		}

		return $result;
	}
}