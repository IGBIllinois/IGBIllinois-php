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
*
*
*/
class cfop {

	const ACTIVITY_CODE_MAX_LENGTH = 6;
	const FOAPAL_DEBUG_URL = "https://api-test.apps.uillinois.edu/finance/foapal-web-service";
	const FOAPAL_PRODUCTION_URL = "https://api.apps.uillinois.edu/finance/foapal-web-service";
	const VALIDATE_ELEMENTS = "/validate-foapal-elements";
	const HEADER_ACCEPT = "application/json";
	const HEADER_CONTENT_TYPE = "application/json";
	
	/** @var PHP Curl Session */
	private $ch; 

	/** @var bool enables debug mode */
	private $debug = false;

	/** @var string cfop api key */
	private $api_key;

	/** @var string transation date */
	private $transaction_date;

	/**
	 * Creates cfop object
	 * @param string $api_key API Key to connect to AITS service
	 * @param bool $debug enables or disable debug mode
	 *
	 * @return \IGBIllinois\cfop
	 */
	public function __construct($api_key,$debug = false) {
		$this->api_key = $api_key;
		$this->debug = $debug;
		$this->set_transaction_date();
	}

        /**
        * Destroys cfop object. Closes curl session
        *
        * @param void
        * @return void
        */
        public function __destruct() {

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
	
	/**
	 * Validates CFOP by checking AITS using curl and json
	 *
	 * @param string $cfop CFOP in format X-XXXXXX-XXXXXX-XXXXXX
	 * @param string $activity_code Activity Code in format XXXXXX
	 *
	 * @throws \Exception
	 *
	 * @return bool true on success false otherwise
	 */
	public function validate_cfop($cfop,$activity_code = "") {
		if (!self::verify_format($cfop,$activity_code)) {
			return false;
		}
		
		list($coasCode,$fundCode,$orgnCode,$progCode) = explode("-",$cfop);
		$request = array(
				'transDate'=>$this->get_transaction_date(),
				'coasCode'=>$coasCode,
				'fundCode'=>$fundCode,
				'orgnCode'=>$orgnCode,
				'progCode'=>$progCode
		);

		if ($activity_code != "") {
			$request['actvCode'] = $activity_code;
			
		}
		$json = json_encode($request);
		$curl_response = array();
		try {
			$curl_response = $this->send_curl($json);
		}
		catch (\Exception $e) {
			throw new \Exception($e->getMessage());
			return false;
			
		}
		$response = json_decode($curl_response,true);
		$valid = $response['Response'][0]['status'];
		$message = $response['Response'][0]['msg'];
		if ($valid == 'Valid') {
			return true;	
		}
		else {
			throw new \Exception($message);
			return false;
		}
	}

	/**
	 * Sends json string to AITS rest api
	 *
	 * @param string $json_payload json formatted string
	 *
	 * @throws \Exception
	 * @return json json response
	 */
	private function send_curl($json_payload) {
		$headers = array(
			'Accept: ' . self::HEADER_ACCEPT,
			'Content-Type: ' . self::HEADER_CONTENT_TYPE,
			'Cache-Control: no-cache',
			'Ocp-Apim-Subscription-Key: ' . $this->api_key
		);
	
		$url = self::FOAPAL_PRODUCTION_URL . self::VALIDATE_ELEMENTS;
		if ($this->debug) {
			$url = self::FOAPAL_DEBUG_URL . self::VALIDATE_ELEMENTS;
		}
		$this->ch = curl_init($url);
		if (!is_resource($this->ch)) {
			throw new \Exception('Curl did not init');
			return false;
		}
		$this->set_curl_settings();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json_payload );
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

		if (! $result = curl_exec($this->ch)) {
			throw new \Exception('Error sending curl');
			return false;

		}
		$httpcode = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
		if ($httpcode != 200) {
			throw new \Exception('Bad response: ' . $httpcode);
			return false;
		}
		//curl_close is only valid for php versions less than 8
		if (version_compare(PHP_VERSION,'8.0.0', '<') && is_resource($this->ch)) {
			curl_close($this->ch);
		}
		return $result;

	}

	/**
	 * Sets Curl Settings
	 *
	 * @param void
	 * @return vaoid
	 */
	private function set_curl_settings() {
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);



	}

	/**
	 * Sets transacation date for json to properly formatted todays date
	 *
	 * @param void
	 * @return void
	 */
	private function set_transaction_date() {
		$this->transaction_date = date('Y-m-d\TH:i:s\Z');

	}

	/**
	 * Gets transaction date for json
	 * 
	 * @param void
	 * @return string properly formatted transaction date
	 */
	private function get_transaction_date() {
		return $this->transaction_date;
	}
}


?>
