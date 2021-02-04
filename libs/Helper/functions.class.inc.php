<?php
/**
* Helper function class
*
*/

namespace IGBIllinois\Helper;

/**
* function class has many helper functions
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois\Helper
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
* @static
*
*/
class functions {

	/**
	* const 
	*/
	const PHP_FILE_UPLOAD_ERRORS = array(
		0 => 'There is no error, the file uploaded with success',
		1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3 => 'The uploaded file was only partially uploaded',
		4 => 'No file was uploaded',
		6 => 'Missing a temporary folder',
		7 => 'Failed to write file to disk.',
		8 => 'A PHP extension stopped the file upload.'
	);

	/**
	* Retrieves enabled php extensions
	*
	* @param void
	* @return string[]
	*/
	public static function get_php_extensions() {
		$chunk_size = 13;
		$extensions = get_loaded_extensions(); 
		natcasesort($extensions);
		$extensions_chunk = array_chunk($extensions,$chunk_size);
		return $extensions_chunk;

	}

	/**
	* Gets Webserver Version
	*
	* @param void
	* @return string
	*/	
	public static function get_webserver_version() {
		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			return $_SERVER['SERVER_SOFTWARE'];
		}
		return "";

	}

	/**
	* Gets php upload error message
	*
	* @param int|null PHP upload error code
	* @return string
	*/
	public static function get_php_upload_error($index = null) {
		if (!is_null($index)) {
			return self::PHP_FILE_UPLOAD_ERRORS[$index];
		}
	}	

	/**
	* Converts various sizes to bytes
	*
	* @param string $size string variable with size + unit suffix
	* @return int 
	*/
	public static function convert_bytes($size) {
		$bytes = 0;
                if ($mem == "") {
                        $bytes = 0;
                }
                else {
			$result = preg_split('#(?<=\d)(?=[a-z])#i', strtolower($mem));
                        $unit = "";
                        if (isset($result[1])) {
                                $unit = $result[1];
                        }
                        $mem = $result[0];
			switch ($unit) {
				case "b":
					$bytes = $mem;
					break;
						
				case "kb":
					$bytes = $mem * 1024;
					break;	
				case "mb":
					$bytes = $mem * 1048576;
					break;
				case "m":
					$bytes = $mem * 1048576;
					break;
				case "gb":
					$bytes = $mem * 1073741824;
					break;
				case "tb":
					$bytes = $mem * 1099511627776;
					break;
				default:
					$bytes =  0;
					break;
			}
		}
		return $bytes;
	}

	/**
	* Gets maximum upload size from php.ini in bytes
	*
	* @params void
	* @return int
	*/
	public static function get_max_upload() {
		return self::convert_bytes(ini_get('upload_max_filesize'));

	}

}
?>
