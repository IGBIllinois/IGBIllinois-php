<?php
/**
* log class write to log file
*
*/

namespace IGBIllinois;

/**
* log class writes to log file
*
* Provides functions to log message to a log file
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
*
*
*
*/
class log {

	private const MAX_FILE_LENGTH = 1000;
	const NOTICE = 0;
	const WARNING = 1;
	const ERROR = 2;

	public static function send_log($message,$log_level = self::NOTICE) {
                $current_time = date('Y-m-d H:i:s');
		$full_msg = $current_time . ": ";
		switch ($log_level) {
			case self::NOTICE:
				$full_msg .= "NOTICE: ";
				break;
			case self::WARNING:
				$full_msg .= "WARING: ";
				break;
			case self::ERROR:
				$full_msg .= "ERROR: ";
				break;

		}
                $full_msg .= $message . "\n";

                if (settings::log_enabled()) {
                        file_put_contents(settings::get_log_file(),$full_msg,FILE_APPEND | LOCK_EX);
                }
                if (php_sapi_name() == "cli") {
                        echo $full_msg;
                }
        }

	public static function get_log() {
		//$contents = file_get_contents(settings::get_log_file(),FALSE,NULL,self::MAX_FILE_LENGTH);
		$contents = file_get_contents(settings::get_log_file());
		return $contents;


	}


}

?>
