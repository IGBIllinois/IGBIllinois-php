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

	const gpfs_replication = 2;
	const gpfs_mmpolicy_du = "/usr/local/bin/mmpolicy-du.pl";
	const kilobytes_to_bytes = "1024";

        /**
        * Gets size of directory
        *
        * @param string $directory Full path to directory
        *
        * @return int amount in bytes
        */
	public static function get_dir_size($directory) {

                $result = false;
		$filesystem_type = self::get_filesystem_type($directory);
		switch ($filesystem_type) {
			case "ceph":
				$result = self::get_dir_size_rbytes($directory);
				break;

			case "gpfs":
				$result = self::get_dir_size_gpfs($directory);
				break;
			default:
				$result = self::get_dir_size_du($directory);
				break;


		}
                return $result;
        }

	/**
	* Gets the type of filesystem the directory is on
	*
	* @param string $directory Full path to directory
	* @return string file system type
	*/
	public static function get_filesystem_type($directory) {
		$result = false;
		if (file_exists($directory)) {
			$exec = "stat --file-system --printf=%T " . $directory;
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
	* @param string $directory Full path to directroy
	* @return int amount in bytes
	*/
	private static function get_dir_size_rbytes($directory) {
		$exec = "stat --printf=%s " . $directory;
		$exit_status = 1;
		$output_array = array();
		$output = exec($exec,$output_array,$exit_status);
		if (!$exit_status) {
			$result = $output;
		}
		return $result;


	}

	/**
	* Gets directory size using du command.
	*
	* @param string $directory Full path to directory
	* @return int amount in bytes
	*/
        private static function get_dir_size_du($directory) {
		$result = 0;
		if (file_exists($directory)) {
                	$exec = "du --max-depth=0 " . $directory . "/ | awk '{print $1}'";
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
	* @param string $directory Full path to directory
	* @return int amount in bytes
	*/
	private static function get_dir_size_gpfs($directory) {

		$result = 0;
                if (file_exists($directory)) {
                        $exec = "source /etc/profile; ";
			$exec .= self::gpfs_mmpolicy_du . " " . $this->get_directory() . "/ | awk '{print $1}'";
                        $exit_status = 1;
                        $output_array = array();
                        $output = exec($exec,$output_array,$exit_status);
                        if (!$exit_status) {
                                $result = round($output * self::kilobytes_to_bytes / self::gpfs_replication );
                        }
                }

		return $result;

	}	


}

?>
