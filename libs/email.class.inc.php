<?php
/**
* email class to send properly formatted emails use PEAR mail_mime, auth_sasl, and net_smtp
*
*/
namespace IGBIllinois;

/**
* email class to send properly formatted emails use PEAR mail_mime, auth_sasl, and net_smtp
*
* Provides interface to easily send emails
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
*
*/

class email {

        ///////////////Private Variables//////////
	/** @var string smtp hostname */ 
        private $smtp_host = "locahost";
	/** @var integer smtp port*/
        private $smtp_port = "25";
	/** @var string smtp username */
        private $smtp_username;
	/** @var string smtp password */
        private $smtp_password;
	/** @var boolean Enable SSL */
        private $smtp_ssl false;
	/** @var boolean Enable TLS */
        private $smtp_tls = false;

        ////////////////Public Functions///////////

	/**
	* Object constructor
	*
	* Makes initial connection to ldap database
	*
	* @param string $host list of ldap hosts seperated by a space
	* @param string $base_dn Ldap base dn
	* @param int $port ldap port the server listens on
	* @param boolean $ssl enable or disable ssl
	* @param boolean $tls enable or disable tls
	* @return \IGBIllinois\ldap
	*/
        public function __construct($smtp_host,$smtp_port,$smtp_username,$smtp_password,$smtp_ssl,$smtp_tls) {
                $this->smtp_host = $smtp_host;
                $this->smtp_port = $smtp_port;
		$this->smtp_username = $smtp_user;
                $this->smtp_password = $smtp_password;
		$this->smtp_ssl = $smtp_ssl;
                $this->smtp_tls = $smtp_tls;
        }

	/**
	* Object deconstructor
	*
	* Destroys ldap object
	* @param void
	* @return void
	*/
        public function __destruct() {}

	/**
	* gets smtp host
	*
	* @param void
	* @return string[]
	*/
        public function get_host() { return $this->smtp_host; }
	
	/**
	* gets smtp port
	*
	* @param void
	* @return integer
	*/
        public function get_port() { return $this->smtp_port; }

	/**
	* gets smtp username
	*
	* @param void
	* @return string
	*/
        public function get_smtp_username() { return $this->smtp_username; }

	/**
	* gets if ssl is enabled
	*
	* @param void
	* @return bool true if enabled, false otherwise
	*/
        public function get_ssl() { return $this->ldap_ssl; }

	/**
	* get if tls is enabled
	*
	* @param void
	* @return bool true if enabled, false otherwise
	*/
	public function get_tls() { return $this->ldap_tls; }


	/**
	* generate email message date header
	* @param void
	* @return date current date in proper formate 
	*/
	private function generate_message_date() {
		return date('D, d M Y H:i:s O');
	}

	/**
	* generate email Message-ID header
	*
	* @param void
	* @return string Message-ID
	*/
	private function generate_message_id() {
		return sprintf(
                        "<%s.%s@%s>",
                        base_convert(microtime(), 10, 36),
                        base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
                        $_SERVER['SERVER_NAME']
                );
	}
}

?>

