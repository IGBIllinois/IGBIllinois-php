<?php
/**
* session class is a class to interface with php session variables
*
*/
namespace IGBIllinois;
/**
* session class is a class to interface with php session variables
*
* Provides all necessary functions to create, use and destroy php session variables
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
class session {


	///////////////Private Variables//////////
	/** @var string name of the session*/
	private $session_name;

	const cookie_samesite = 'lax';
	const cookie_secure = true;
	const cookie_httponly = true;
	const cookie_lifetime = 0;

        ////////////////Public Functions///////////

	/**
	* Constructs session object
	*
	* Constructs object, sets session settings, and starts the session
	*
	* @param string $session_name name of the session.  Should be unique to application
	*
	* @return \IGBIllinois\session
	*/
        public function __construct($session_name) {
		$this->session_name = $session_name;
		$this->set_settings();
		$this->start_session();		
        }

	/**
	* Destructs session object
	*
	* @param void
	* @return void
	*/
	public function __destruct() {}

	/** 
	* Returns value from session variable
	*
	* @param string $name name of session variable
	* @return string value of session variable
	*/
	public function get_var($name) {
		$result = false;
		if ($this->is_session_started() && (isset($_SESSION[$name]))) {
			$result = $_SESSION[$name];
		}
		return $result;

	}

	/**
	* Returns array of all session variables
	*
	* @param void
	* @return array associative array of key->value session variables
	*/
	public function get_all_vars() {
		return $_SESSION;

	}

	/**
	* Sets session variables
	*
	* @param array $session_array an associative array of $key->$var session variables
	* @return void
	*/
	public function set_session($session_array) {
		foreach ($session_array as $key=>$var) {
			$this->set_session_var($key,$var);
		}

	}

	/** 
	* Returns current session id
	*
	* @param void
	* @return int current session id
	*/
	public function get_session_id() { 
		return session_id();
	}

	/**
	* Gets session name
	*
	* @param void
	* @return string session name
	*/
	public function get_session_name() {
		return $this->session_name;
	}

	/**
	* Destroys all session variables
	*
	* Destroys session variables and regenerates session id
	*
	* @param void
	* @return void
	*/
	public function destroy_session() {
		if ($this->is_session_started()) {
			session_unset();
			session_destroy();
			session_write_close();
		}
	}

	/**
	* Sets session variable
	* 
	* @param string $name name of session variable
	* @param string @var value of session variable
	* @return bool true on success false otherwise
	*/
        public function set_session_var($name,$var) {
                $result = false;
                if ($this->is_session_started()) {
                        $_SESSION[$name] = $var;
                        $result = true;
                }
                return $result;

        }

	////////////////Private Functions/////////////

	/**
	* Starts Session
	*
	* @param void
	* @return void
	*/
	private function start_session() {
		session_name($this->session_name);

		session_start();
			
        }

	/** 
	* Checks if session is started
	*
	* @param void
	* @return boolean true if started, false otherwise
	*/
	private function is_session_started() {
		$result = false;
		if ($this->get_session_id() != "") {
			$result = true;
		}
		return $result;
	}

	/**
	* Sets session settings
	*
	* Sets settings to ensure high level of encryption
	*
	* @param void
	* @return void
	*/
	private function set_settings() {
		
		session_set_cookie_params(self::cookie_lifetime, 
			'/; samesite='.self::cookie_samesite, 
			$_SERVER['HTTP_HOST'], 
			self::cookie_secure, 
			self::cookie_httponly);	
		$session_hash = "sha512";
		if (in_array($session_hash,hash_algos())) {
			ini_set("session.hash_function","sha512");
		}
		ini_set("session.entropy_file","/dev/urandom");
		ini_set("session.entropy_length","512");
		ini_set("session.hash_bits_per_character",6);
	}

}

?>
