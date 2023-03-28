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

	const FBS_URL = "https://fbs.engr.illinois.edu/API";
	const LOGIN = "/auth/login";
	const HEADER_ACCEPT = "application/json";
	const HEADER_CONTENT_TYPE = "application/json";
	
	/** @var PHP Curl Session */
	private $ch; 

	/** @var string access key */
	private $access_key;

	/** @var string secret key */
	private $secret_key;

	/** @var string token key */
	private $token;

	/**
	 * Creates fbs object
	 * @param string $access_key API Access Key to connect to FBS Service
	 * @param string $secret_key API Secret Key to connect to FBS Service
	 *
	 * @return \IGBIllinois\fbs
	 */
	public function __construct($access_key,$secret_key) {
		$this->access_key = $access_key;
		$this->secret_key = $secret_key;
	}

        /**
        * Destroys fbs object. Closes curl session
        *
        * @param void
        * @return void
        */
        public function __destruct() {

        }

	/**
	 * Login to Rest API using access and secret key
	 *
	 * @param void
	 * @return bool true on success, false otherwise
	 */
	public function login() {
                $login_array = array('AccessKeyId'=>$this->access_key,
                                'SecretAccessKey'=>$this->secret_key
                        );
                $login_json = json_encode($login_array);
                $url = self::FBS_URL . self::LOGIN;
                try {
                        $result = $this->post($url,$login_json);

                }
                catch (\Exception $e) {
                        throw $e;
                        return false;
                }
                $response = json_decode($result,true);
                if (isset($response['Token'])) {
                        $this->token = $response['Token'];
                        return true;
                }
                return false;

        }

	/**
	 * Gets List of customers
	 *
	 * @param int Facility ID number
	 *
	 * @throws \Exception
	 * @return string[] an array of customers
	 */
        public function get_customers($facility_id) {
                $header = array('Authorization: Bearer ' . $this->token);
                $url = self::FBS_URL . "/FBS/Facility/" . $facility_id . "/Customer";
                try {
                        $result = $this->get($url,$header);

                }
                catch (\Exception $e) {
                        throw $e;
                        return false;
                }
                $response = json_decode($result,true);
                return $response;

        }
	
	/**
	 * Sends http post to FBS rest api
	 *
	 * @param string $url url to contact
	 * @param json $json_payload json formatted string
	 * @param string[] $additional_headers An array of html headers to add
	 *
	 * @throws \Exception
	 * @return json json response
	 */
	private function post($url,$json_payload,$additional_headers = array()) {
		$headers = array(
			'Accept: ' . self::HEADER_ACCEPT,
			'Content-Type: ' . self::HEADER_CONTENT_TYPE,
			'Cache-Control: no-cache'
		);
		$headers = array_merge($headers,$additional_headers);
	
		$this->ch = curl_init($url);
		if (!is_resource($this->ch)) {
			throw new \Exception('Curl did not init');
			return false;
		}
		$this->set_curl_settings();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
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
	 * Sends http get to FBS rest api
	 *
	 * @param string $url URL to contact
	 * @param string[] @additional_headers An array of http headers to add
	 *
	 * @throws \Exception
	 * @returns json json response
	 */
	private function get($url,$additional_headers) {
                $headers = array(
                        'Accept: ' . self::HEADER_ACCEPT,
                        'Content-Type: ' . self::HEADER_CONTENT_TYPE,
                        'Cache-Control: no-cache'
                );
                $headers = array_merge($headers,$additional_headers);
                $this->ch = curl_init($url);
                if (!is_resource($this->ch)) {
                        throw new \Exception('Curl did not init');
                        return false;
                }
		
		$this->set_curl_settings();
                curl_setopt($this->ch, CURLOPT_URL, $url);
                curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true );
	
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
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);



	}

}


?>
