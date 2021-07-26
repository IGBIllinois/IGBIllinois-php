<?php
/**
* Helper date functions
*
*/

namespace IGBIllinois\Helper;

/**
* date_helper class with date helper functions
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois\Helper
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
* @static
*
*/
class date_helper {


	/**
	* Verify valid date
	*
	* @param string $inDate 
	* @return bool true if valid, false otherwise
	*/
	public static function verify_date($inDate) {
		$format = "Y-m-d H:i:s";
		$date = \DateTime::createFromFormat($format,$inDate);
		return $date && ($date->format($format) === $inDate);
		
	}

	/**
	* Formats date to YYYY/MM/DD
	* 
	* @param string $date in form YYYYMMDD
	* @return string in form YYYY/MM/DD
	*/
	public static function get_pretty_date($date) {
		return substr($date,0,4) . "/" . substr($date,4,2) . "/" . substr($date,6,2);

	}
}

?>
