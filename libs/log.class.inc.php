<?php
/**
* log class to write and read a log file
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
*/
class log {

	/** @var boolean enables or disables log */
	private $enabled = false;
	/** $var string full path to log file */
	private $logfile = ""; 
	/** $const maximum number of lines to retrieve */ 
	const MAX_FILE_LENGTH = 1000;
	/** $const notice constant */ 
	const NOTICE = 0;
	/** $const warning constant */
	const WARNING = 1;
	/** $const error constant */
	const ERROR = 2;

        /**
        * Creates log object
        *
        * @param boolean @enabled enables or disables log
        * @param string @logfile full path to log file
        *
        * @return void
        */

        public function __construct($enabled = false,$logfile) {
		$this->enabled = $enabled;
		$this->logfile = $logfile;


        }

	/**
	* Sends log message
	*
	* @param string $message Log message to send
	* @param int $log_level Log level to use
	*
	* @return void
	*/
	public function send_log($message,$log_level = self::NOTICE) {
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

                if ($this->enabled) {
                        file_put_contents($this->logfile,$full_msg,FILE_APPEND | LOCK_EX);
                }
                if (php_sapi_name() == "cli") {
                        echo $full_msg;
                }
        }

	/**
	* Retrieve log file contents
	*
	* $param void
	* $return string contents of log file
	*/
	public function get_log() {
		$contents = file_get_contents($this->logfile);
		return $contents;


	}


}

?>
