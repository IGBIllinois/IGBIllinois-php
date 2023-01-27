<?php
/**
* cfop class provides common CFOP functions
*
*/
namespace IGBIllinois;

/**
* cfop class provides common CFOP functions
*
* Provides functions for CFOPs
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
*
*/
class cfop {

	private const ACTIVITY_CODE_MAX_LENGTH = 6;
	/**
        * Verify cfop format
        *
	* @param string $cfop
	* @param string $activity_code
        * @return bool true if valid, false otherwise
        */
	public static function verify_format($cfop,$activity_code = "") {
		$error = false;

		//Check CFOP
		if (!preg_match('^[1-3]{1}-[0-9]{6}-[0-9]{6}-[0-9]{6}$^',$cfop)) {
			$error = true;
		}

		//Check Activity Code
		if ((strlen($activity_code) > 0 
			&& strlen($activity_code) <= self::ACTIVITY_CODE_MAX_LENGTH) 
			&& (!preg_match('^[a-zA-Z0-9]^',$activity_code))) {

			$error = true;

		}
		elseif (strlen($activity_code > self::ACTIVITY_CODE_MAX_LENGTH)) {
			$error = true;
		}

		if ($error) {
			return false;
		}
                return true;


        }
	

}


?>
