<?php
/**
* Generate smbhash passwords
*
*/

namespace IGBIllinois\Helper;

/**
* smbhash class with functiosn to generate smb passwords
*
* @author Joe Leigh <jleigh@illinois.edu>
* @access public
* @package IGBIllinois\Helper
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
* @static
*
*/
class smbhash {

	/**
        * Create NTLM Password hash
        *
        * @param string $password clear text password
        * @return string MD4Hash of password
        */
	public static function NTLMHash($password)
	{
		// Convert to UTF16 little endian
		$password = iconv('UTF-8', 'UTF-16LE', $password);
		//Encrypt with MD4
		$MD4Hash = hash('md4', $password);
		return strtoupper($MD4Hash);
	}

	/**
        * Create LM password hash
        *
        * @param string $password cleartext password
        * @return string LMHash of password
        */

	public static function LMhash($password)
	{
		$password = strtoupper(substr($password, 0, 14));
		$p1 = self::LMhash_DESencrypt(substr($password, 0, 7));
		$p2 = self::LMhash_DESencrypt(substr($password, 7, 7));
		return strtoupper($p1 . $p2);
	}

	/**
        * Create DES encrypt of LMhash
        *
        * @param string $string
        * @return string DES encrypt of LMhash
        */
	private static function LMhash_DESencrypt($string)
	{
		$key = [];
		$tmp = [];
		$len = strlen($string);

		for ($i = 0; $i < 7; ++$i) {
			$tmp[] = $i < $len ? ord($string[$i]) : 0;
		}

		$key[] = $tmp[0] & 254;
		$key[] = ($tmp[0] << 7) | ($tmp[1] >> 1);
		$key[] = ($tmp[1] << 6) | ($tmp[2] >> 2);
		$key[] = ($tmp[2] << 5) | ($tmp[3] >> 3);
		$key[] = ($tmp[3] << 4) | ($tmp[4] >> 4);
		$key[] = ($tmp[4] << 3) | ($tmp[5] >> 5);
		$key[] = ($tmp[5] << 2) | ($tmp[6] >> 6);
		$key[] = $tmp[6] << 1;

		$key0 = "";

		foreach ($key as $k) {
			$key0 .= chr($k);
		}

		$crypt = openssl_encrypt(
			"KGS!@#$%",
			'des-ecb',
			$key0,
			true/*OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING*/
		); // TODO These constants won't exist until php 5.4

		return bin2hex($crypt);
	}

}

?>

