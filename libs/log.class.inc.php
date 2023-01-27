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
	/** $var boolean enable output to standard out */
	private $stdout = true;
	/** $var maximum number of lines to retrieve */ 
	const MAX_FILE_LENGTH = 1000;
	/** $var notice constant */ 
	const NOTICE = 0;
	/** $var warning constant */
	const WARNING = 1;
	/** $var error constant */
	const ERROR = 2;

        /**
        * Creates log object
        *
        * @param boolean $enabled enables or disables log
        * @param string $logfile full path to log file
        *
        * @return \IGBIllinois\log
        */
        public function __construct($enabled = false,$logfile,$stdout=true) {
		$this->enabled = $enabled;
		$this->logfile = $logfile;
		$this->stdout = $stdout;

		if (($this->enabled) && !file_exists($this->logfile)) {
			touch($this->logfile);
		}
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
				$full_msg .= "WARNING: ";
				break;
			case self::ERROR:
				$full_msg .= "ERROR: ";
				break;
			default:
				$full_msg .= "NOTICE: ";
				break;

		}
                $full_msg .= $message . "\n";

                if ($this->enabled) {
                        file_put_contents($this->logfile,$full_msg,FILE_APPEND | LOCK_EX);
                }
                if ((php_sapi_name() == "cli") && ($stdout)) {
                        echo $full_msg;
                }
        }

	/**
	* Retrieve log file contents
	*
	* $param void
	* $return string contents of log file, false otherwise
	*/
	public function get_log() {
		if (file_exists($this->logfile)) {
			$contents = file_get_contents($this->logfile);
			return $contents;
		}
		return false;


	}


}

?>
