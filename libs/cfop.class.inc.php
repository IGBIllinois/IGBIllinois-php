<?php
/**
* cfop class provides common CFOP functions
*
*/
namespace IGBIllinois;

/**
* cfop class provides common CFOP functions and can check FOAPAL web services API to validate a CFOP
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
	private const FOAPAL_DEBUG_URL = "https://api-test.apps.uillinois.edu/finance/foapal-web-service";
	private const FOAPAL_PRODUCTION_URL = "https://api.apps.uillinois.edu/finance/foapal-web-service";
	private const VALIDATE_ELEMENTS = "/validate-foapal-elements";
	private const HEADER_ACCEPT = "application/json";
	private const HEADER_CONTENT_TYPE = "applicaiton/json";

	/** @var PHP Curl Session */
	private $ch 

	/** @var enable debug */
	private $debug = false;
	
	public function __construct($debug = false) {
		$this->debug = $debug;

	}

        /**
        * Destroys cfop object. Closes curl session
        *
        * @param void
        * @return void
        */
        public function __destruct() {
                curl_close($this->ch);

        }

	
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
		elseif (strlen($activity_code) > self::ACTIVITY_CODE_MAX_LENGTH) {
			$error = true;
		}

		if ($error) {
			return false;
		}
                return true;


        }
	

	public function validate_cfop($cfop,$activity_code = "") {
		if (!self::verify_format($cfop,$activity_code)) {
			return false;
		}
		
		list($coasCode,$fundCode,$orgnCode,$progCode) = explode("-",$cfop);
		$request = array(
				'coasCode'=>$coasCode,
				'fundCode'=>$fundCode,
				'orgnCode'->$orgnCode,
				'progCode'=>$progCode
		);

		if ($activity_code != "") {
			$request['actvCode'] = $activity_code;
			
		}
		$json = json_encode($request);

		try (
			$curl_result = $this->send_curl($json);
		}
		catch ($e as Exception) {
			echo $e->getMessage();
		}
		$result = json_decode($curl_result);
		var_dump($result);
	}

	private function send_curl($json_payload) {
		$header = array(
			'Accept: ' . self::HEADER_ACCEPT,
			'Content-Type: ' . self::HEADER_CONTENT_TYPE
		);

		if ($this->debug) {
			$this->ch = curl_init(self::FOAPAL_DEBUG_URL . self::VALIDATE_FOAPAL_ELEMENTS);
		}
		else {
			$this->ch = curl_init(self::FOAPAL_PRODUCTION_URL . self::VALIDATE_FOAPAL_ELEMENTS);			

		}
		curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, false );
		if (! $result = curl_exec($this->ch)) {
			throw new Exception('Error sending curl');

		}



	}
}


?>
