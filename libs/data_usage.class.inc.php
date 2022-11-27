<?php
/**
* data_usage class calculates amount of data in a directory
*
*/
namespace IGBIllinois;

/**
* data_usage class calculates amount of data in a directory
*
* Provides functions to calculate amount of data in a directory
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
*
*/
class data_usage {

	private $directory;

	const GPFS_REPLICATION = 2;
	const GPFS_MMPOLICY_DU = "/usr/local/bin/mmpolicy-du.pl";
	const KILOBYTES_TO_BYTES = "1024";

	/*
	 * Object Constructor
	 *
        * Creates data_usage object
        *
        * @param string $directory Full Path to directory
        * @return \IGBIllinois\data_usage
        */
        public function __construct($directory) {
                $this->directory = self::format_directory($directory);;

        }

	/**
	* Gets directory
	*
	* @param void 
	* @return string Full path to directory
	*/
	public function get_directory() {
		return $this->directory;
	}

	/**
	* Test if directory exists
	*
	* @param void
	* @return boolean true on success, false otherwise
	*/
	public function directory_exists() {
		if (true === file_exists($this->get_directory())) {
                        return 1;
                }
                return 0;

	}

	
        /**
        * Gets size of directory
	*
        * @param void
        * @return int amount in bytes
        */
	public function get_dir_size() {
		$result = 0;
		if ($this->directory_exists()) {
			$filesystem_type = self::get_filesystem_type();
			try {
				switch ($filesystem_type) {
					case "ceph":
						$result = self::get_dir_size_rbytes();
						break;
					case "gpfs":
						$result = self::get_dir_size_gpfs();
						break;
					default:
						$result = self::get_dir_size_du();
						break;


				}
			}
			catch (\Exception $e) {
				throw $e;
			}
		}
		return $result;
        }

	/**
	* Gets the type of filesystem the directory is on
	*
	* @param void
	* @return string file system type
	*/
	public function get_filesystem_type() {
		$result = false;
		if (file_exists($this->get_directory())) {
			$exec = "stat --file-system --printf=%T " . $this->get_directory();
	                $exit_status = 1;
        	        $output_array = array();
                	$output = exec($exec,$output_array,$exit_status);
	                if (!$exit_status) {
        	                $result = $output;
                	}
		}
		return $result;

	}

	/**
	* Gets directory size using rbytes field.  Used with Ceph filesystem
	*
	* @param void
	* @return int amount in bytes
	*/
	private function get_dir_size_rbytes() {

		$result = 0;
		if (file_exists($this->get_directory())) {
			$exec = "stat --printf=%s " . $this->get_directory();
			$exit_status = 1;
			$output_array = array();
			$output = exec($exec,$output_array,$exit_status);
			if (!$exit_status) {
				$result = $output;
			}
		}
		return $result;


	}

	/**
	* Gets directory size using du command.
	*
	* @param void
	* @return int amount in bytes
	*/
        private function get_dir_size_du() {
		$result = 0;
		if (file_exists($this->get_directory())) {
                	$exec = "du --block-size=1 --max-depth=0 " . $this->get_directory() . "/ | awk '{print $1}'";
	                $exit_status = 1;
        	        $output_array = array();
                	$output = exec($exec,$output_array,$exit_status);
	                if (!$exit_status) {
        	                $result = $output;
                	}
		}
                return $result;


        }

	/**
	* Gets directory size for gpfs filesystem.  Uses mmpolicy du
	*
	* @param void
	* @return int amount in bytes
	*/
	private function get_dir_size_gpfs() {

		$result = 0;
                if (file_exists($this->get_directory())) {
                        $exec = "source /etc/profile && ";
			#$exec .= self::gpfs_mmpolicy_du . " -k " . $this->get_directory() . "/ | awk '{print $1}'";
			$exec .= self::GPFS_MMPOLICY_DU . " -k " . $this->get_directory() . " 2>/dev/null";
                        $exit_status = 1;
                        $output_array = array();
                        $output = exec($exec,$output_array,$exit_status);
                        if (!$exit_status) {
				$size = explode(" ",$output)[0];
                                $result = round($size * self::KILOBYTES_TO_BYTES / self::GPFS_REPLICATION );
                        }
			else {
				throw new \Exception("Error running " . self::GPFS_MMPOLICY_DU);
			}
                }

		return $result;

	}	


	/**
	* Removes trailing slash at end of directory path
	*
	* @param string $directory Full path to directory
	* @returns string path to directory
	*/	
	private static function format_directory($directory) {
                if (strrpos($directory,"/") == strlen($directory) -1) {
                        return substr($directory,0,strlen($directory)-1);
                }
                else {
                        return $directory;
                }

        }

}

?>
