<?php
/**
* fbs class provides common CFOP functions
*
*/
namespace IGBIllinois;

/**
* fbs class provides functions to interact with FBS Rest API
*
* Provides functions for FBS
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
class fbs {

	const FOAPAL_DEBUG_URL = "https://api-test.apps.uillinois.edu/finance/foapal-web-service";
	const FOAPAL_PRODUCTION_URL = "https://api.apps.uillinois.edu/foapal-web-service";
	const VALIDATE_ELEMENTS = "/validate-foapal-elements";
	const HEADER_ACCEPT = "application/json";
	const HEADER_CONTENT_TYPE = "application/json";
	
	/** @var PHP Curl Session */
	private $ch; 

	/** @var bool enables debug mode */
	private $debug = false;

	/** @var string access key */
	private $access_key;

	/** @var string secret key */
	private $secret_key;

	/**
	 * Creates fbs object
	 * @param string $access_key API Access Key to connect to FBS Service
	 * @param string $secret_key API Secret Key to connect to FBS Service
	 * @param bool $debug enables or disable debug mode
	 *
	 * @return \IGBIllinois\fbs
	 */
	public function __construct($access_key,$secret_key,$debug = false) {
		$this->access_key = $access_key;
		$this->secret_key = $secret_key;
		$this->debug = $debug;
	}

        /**
        * Destroys fbs object. Closes curl session
        *
        * @param void
        * @return void
        */
        public function __destruct() {

        }

	
	/*
	 * Sends json string to FBS rest api
	 *
	 * @param json $json_payload json formatted string
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

}


?>
