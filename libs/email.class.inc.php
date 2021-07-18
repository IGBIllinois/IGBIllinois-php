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

        ////////////////Public Functions///////////

	/**
	* Object constructor
	*
	* Makes initial connection to ldap database
	*
	* @param string $smtp_host SMTP Hostname
	* @param string $smtp_port SMTP Port. Defaults to 25
	* @param string $smtp_username SMTP Username. Optional
	* @param string $smtp_password SMTP Password. Optional
	* @return \IGBIllinois\ldap
	*/
        public function __construct($smtp_host,$smtp_port,$smtp_username = "",$smtp_password = "") {
                $this->smtp_host = $smtp_host;
                $this->smtp_port = $smtp_port;
		$this->smtp_username = $smtp_username;
                $this->smtp_password = $smtp_password;
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
        public function get_smtp_host() { return $this->smtp_host; }
	
	/**
	* gets smtp port
	*
	* @param void
	* @return integer
	*/
        public function get_smtp_port() { return $this->smtp_port; }

	/**
	* gets smtp username
	*
	* @param void
	* @return string
	*/
        public function get_smtp_username() { return $this->smtp_username; }

	/**
	* get smtp password
	*
	* @param void
	* @return string
	*/
	public function get_smtp_password() { return $this->smtp_password; }

	/**
	* Sends email
	* 
	* @param string $to To Email Address
	* @param string $from From Email Address
	* @param string $subject Email Subject
	* @param string $txt_message Email Message in plain txt
	* @param string $html_message Email Message in HTML
	* @throws Exception
	* @return boolean True on success, false otherwise
	*/
	public function send_email($to,$from,$subject,$txt_message = "",$html_message = "") {
		if (!filter_var($to,FILTER_VALIDATE_EMAIL)) {
			throw new \Exception("To: Email is invalid");
			return false;
		}
		if (!filter_var($from,FILTER_VALIDATE_EMAIL)) {
			throw new \Exception("From: Email is invalid");
			return false;
		}
		$extraheaders['To'] = $to;
		$extraheaders['From'] = $from;
		$extraheaders['Subject'] = $subject;
		array_push($extraheaders,self::generate_message_date());
		array_push($extraheaders,self::generate_message_id());

		$message = new \Mail_mime();
		if ($txt_message !== "") {
			$message->setTxtBody($txt_message);
		}
		if ($html_message !== "") {
			$message->setHtmlBody($txt_message);
		}
		$headers = $message->headers($extraheaders);
		$body = $message->get();
		$mail_params = $this->get_mail_params();	
		$smtp = \Mail::factory("smtp",$mail_params);
		if (\PEAR::isError($smtp)) {
			throw new \Exception($smtp->getMessage());	
			return false;
		}
		$mail = $smtp->send($to,$headers,$body);
		if (\PEAR::isError($mail)) {
			throw new \Exception($mail->getMessage());
			return false;
		}
		return true;
	}

	/**
	* generates mail params array for Mail::factory
	* @params void
	* $teturn string[] array of mail params
	*/
	private function get_mail_params() {
		$mail_params['host'] = $this->get_smtp_host();
                $mail_params['port'] = $this->get_smtp_port();

                if ($this->get_smtp_username() && $this->get_smtp_password()) {
                        $mail_params['auth'] = 'PLAIN';
                        $mail_params['username'] = $this->get_smtp_username();
                        $mail_params['password'] = $this->get_smtp_password();
                }

		return $mail_params;
	}
	/**
	* generate email message date header
	* @param void
	* @return date[] current date in proper formate 
	*/
	private function generate_message_date() {
		return array('Date'=>date('D, d M Y H:i:s O'));
	}

	/**
	* generate email Message-ID header
	*
	* @param void
	* @return string[] Message-ID
	*/
	private function generate_message_id() {
		
		$message_id = sprintf(
                        "<%s.%s@%s>",
                        base_convert(microtime(), 10, 36),
                        base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
                        $_SERVER['SERVER_NAME']
                );
		return array('Message-Id'=>$message_id);
	}
}

?>

