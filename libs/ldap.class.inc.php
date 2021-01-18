<?php
/**
* ldap class interacts with ldap server using built in php ldap functions
*
*/
namespace IGBIllinois;

/**
* ldap class interacts with ldap server using built in php ldap functions
*
* Provides all necessary functions to connect and run ldap queries
*
* @author David Slater <dslater@illinois.edu>
* @access public
* @package IGBIllinois
* @copyright Copyright (c) 2020 University of Illinois Board of Trustees
* @license https://opensource.org/licenses/GPL-3.0 GNU Public License v3
*
*/

class ldap {

        ///////////////Private Variables//////////
	/** @var resource Ldap resource link */ 
        private $ldap_resource = false;
	/** @var array an array of ldap hosts */
        private $ldap_host = array();
	/** @var string Ldap base dn */
        private $ldap_base_dn;
	/** @var string Ldap bind user */
        private $ldap_bind_user;
	/** @var string Ldap bind password */
        private $ldap_bind_pass;
	/** @var boolean Enable ssl */
        private $ldap_ssl = false;
	/** @var boolean Enable TLS */
	private $ldap_tls = false;
	/** @var int Ldap port */
        private $ldap_port = 389;
	/** @var int Ldap protocol - 2,3 */
        private $ldap_protocol = 3;    

        ////////////////Public Functions///////////

	/**
	* Object constructor
	*
	* Makes initial connection to ldap database
	*
	* @param string $host list of ldap hosts seperated by a space
	* @param boolean $ssl enable or disable ssl
	* @param int $port ldap port the server listens on
	* @param string $base_dn Ldap base dn
	* @return \IGBIllinois\ldap
	*/
        public function __construct($host,$base_dn,$port,$ssl,$tls) {
                $this->set_host($host);
                $this->set_ssl($ssl);
		$this->set_tls($tls);
                $this->set_port($port);
		$this->set_base_dn($base_dn);
                $this->connect();
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
	* gets ldap host
	*
	* @param void
	* @return string[]
	*/
        public function get_host() { return $this->ldap_host; }
	
	/**
	* gets ldap base dn
	*
	* @param void
	* @return string
	*/
        public function get_base_dn() { return $this->ldap_base_dn; }

	/**
	* gets bind user
	*
	* @param void
	* @return string
	*/
        public function get_bind_user() { return $this->ldap_bind_user; }

	/**
	* gets if ssl is enabled
	*
	* @param void
	* @return bool true if enabled, false otherwise
	*/
        public function get_ssl() { return $this->ldap_ssl; }

	/** 
	* gets ldap port
	*
	* @param void
	* @return int
	*/
        public function get_port() { return $this->ldap_port; }

	/**
	* gets ldap protocol
	* 
	* @param void
	* @return int 2 or 3
	*/
        public function get_protocol() { return $this->ldap_protocol; }

	/**
	* gets ldap resource
	*
	* @param void
	* @return resource
	*/
        public function get_resource() { return $this->ldap_resource; }

	/**
	* gets if ldap is connected
	*
	* @param void
	* @return bool true if connected, false otherwise
	*/
	public function get_connection() {
			return is_resource($this->ldap_resource);
	}
        
	/**
	* sets ldap protocol
	*
	* @param int $ldap_protocol protocol version 2 or 3
	* @return void
	*/
	public function set_protocol($ldap_protocol) {
                $this->ldap_protocol = $ldap_protocol;
                ldap_set_option($this->get_resource(),LDAP_OPT_PROTOCOL_VERSION,$ldap_protocol);
        }


	/**
	* binds to ldap server
	*
	* @param string $rdn full rdn of the user
	* @param string $password user password
	* @return bool true on success, false otherwise
	*/
        public function bind($rdn = "",$password = "") {
		$result = false;
		if ($this->get_connection()) {
			if (($rdn != "") && ($password != "")) {
				$result = @ldap_bind($this->get_resource(), $rdn, $password);

			}
			elseif (($rdn == "") && ($password == "")) {
				$result = @ldap_bind($this->get_resource());
			}
		}
		return $result;

        }


	/**
	* searches ldap server
	*
	* @param string $filter ldap search filter
	* @param string $ou organizational unit to search
	* @param string[] $attributes an array of attributes to retrieve
	* @return resource ldap search result identifier
	*/
        public function search($filter,$ou = "",$attributes = array()) {
		$result = false;
		if ($ou == "") {
			$ou = $this->get_base_dn();
		}
		if (($this->get_connection()) && ($attributes != "")) {
	                $ldap_result = ldap_search($this->get_resource(),$ou,$filter,$attributes);
	                $result = ldap_get_entries($this->get_resource(),$ldap_result);
		}
		elseif (($this->get_connection()) && ($attributes == "")) {
			$ldap_result = ldap_search($this->get_resource(),$ou,$filter);
                        $result = ldap_get_entries($this->get_resource(),$ldap_result);

		}
		return $result;

        }

	/**
	* replace ldap entry values with new values
	*
	* @param string $rdn full rdn of entry
	* @param string[] $entries an associative array of attributes and values to replace
	* @return bool true on success, false otherwise
	*/
	public function replace($rdn,$entries) {
		if(ldap_mod_replace($this->get_resource(), $rdn, $entries))
                {
                        return true;
                }
                else{
                        return false;
                }
	}

	/**
	* checks if the ldap user exists
	*
	* @param string $username username of the user
	* @return bool true if exist, false otherwise
	*/
	public function is_ldap_user($username) {
                $username = trim(rtrim($username));
                $filter = "(uid=" . $username . ")";
                $attributes = array('');
                $result = $this->search($filter,"",$attributes);
                if ($result['count']) {
                        return true;
                }
                else {
                        return false;
                }


        }

	/**
	* gets group members of a ldap group
	*
	* @param string $group groupname
	* @return string[] array of group members
	*/
        public function get_group_members($group) {
                if ($this->get_connection()) {
                        $group = trim(rtrim($group));
                        $filter = "(cn=" . $group . ")";
                        $attributes = array('memberUid');
                        $result = $this->search($filter,"",$attributes);
                        unset($result[0]['memberuid']['count']);
                        $members = array();
                        foreach ($result[0]['memberuid'] as $row) {
                                array_push($members,$row);
                        }
                        return $members;
                }
        }

	/**
	* checks if user is member of group
	*
	* @param string $username username
	* @param string $group group name
	* @return bool true if in group, false otherwise
	*/
        public function is_group_member($username,$group) {
                $group_members = $this->get_group_members($group);
                return in_array($username,$group_members);

        }

	/**
	* gets groups a user is a member of
	*
	* @param string $username username
	* @return string[] array of groups
	*/
	public function get_user_groups($username) {
                if ($this->get_connection()) {
                        $username = trim(rtrim($username));
                        $filter = "(&(cn=*)(memberUid=" . $username . "))";
                        $attributes = array('cn');
                        $result = $this->search($filter,"",$attributes);
                        unset($result['count']);
                        $groups = array();
                        foreach ($result as $row) {
                                array_push($groups,$row['cn'][0]);
                        }
                        return $groups;
                }


        }

	/**
	* checks if group exists
	*
	* @param string $group name of group
	* @return bool true if exist, false otherwise
	*/
        public function get_group_exists($group) {
                if ($this->get_connection()) {
                        $group = trim(rtrim($group));
                        $filter = "(cn=" . $group . ")";
                        $attributes = array('');
                        $result = $this->search($filter,"",$attributes);
                        if ($result['count']) {
                                return true;
                        }
                        return false;
                }

        }

	/**
	* retrieves user's home folder attribute
	* 
	* @param string $username username
	* @return string|bool home folder, false if attribute doesn't exist
	*/
	public function get_home_dir($username) {
                if ($this->get_connection()) {
                        $username = trim(rtrim($username));
                        $filter = "(uid=" . $username . ")";
                        $attributes = array('homeDirectory');
                        $result = $this->search($filter,"",$attributes);
                        if ($result['count']) {

                                return $result[0]['homedirectory'][0];
                        }
                        else {
                                return false;
                        }
                }


        }

	/**
	* retrieves user's email attribute
	*
	* @param string $username username
	* @return string|bool email address, false if attribute doesn't exist
	*/
        public function get_email($username) {
                if ($this->get_connection()) {
                        $username = trim(rtrim($username));
                        $filter = "(uid=" . $username . ")";
                        $attributes = array('mail');
                        $result = $this->search($filter,"",$attributes);
                        if (($result['count']) && (isset($result[0]['mail'][0]))){
				return $result[0]['mail'][0];
			
                        }
                        else {
                                return false;
                        }
                }


        }
	
	/**
	* retrieves all users
	*
	* @param void
	* @return string[] an array of users
	*/
	public function get_all_users() {
		$users_array = array();
		if ($this->get_connection()) {
                        $filter = "(objectClass=posixAccount)";
                        $attributes = array('uid');
                        $result = $this->search($filter,"",$attributes);
			foreach ($result as $user) {
				array_push($users_array,$user['uid'][0]);

			}
				
                }
		return $users_array;


	}

	/**
	* retrieves users full name, cn attribute
	*
	* @param string $username username
	* @return string|bool fullname, false if attribute doesn't exist
	*/
        public function get_ldap_full_name($username) {
                if ($this->get_connection()) {
                        $username = trim(rtrim($username));
                        $filter = "(uid=" . $username . ")";
                        $attributes = array("cn");
                        $result = $this->search($filter,"",$attributes);
                        return $result[0]['cn'][0];
                }
                else { return false;
                }
        }

	/**
	* gets ldap error message
	*
	* @param void
	* @return string|bool error message or false otherwise
	*/
	public function get_error() {
		if ($this->get_connection()) {
			return ldap_error($this->get_resource());
		} else {
			return false;
		}
	}

	/**
	* Sets ldap hostname
	*
	* Takes string of single or multiple hostnames and puts them in ldap_host array
	*
	* @param string $ldap_host string of ldap hosts seperated by a space
	* @return void
	*/
	private function set_host($ldap_host) { 
		$this->ldap_host = explode(" ",$ldap_host);
	}

	/**
	* Sets base dn
	*
	* @param string $ldap_base_dn Ldap base dn
	* @return void
	*/
        private function set_base_dn($ldap_base_dn) { $this->ldap_base_dn = $ldap_base_dn; }
	
	/**
	* Sets SSL
	*
	* @param boolean $ldap_ssl true to enable, false to disable
	* @return void
	*/
        private function set_ssl($ldap_ssl) { $this->ldap_ssl = $ldap_ssl; }

	/**
	* Sets TLS
	*
	* @param boolean $ldap_tls true to enable, false to disable
	* @return void
	*/
	private function set_tls($ldap_tls) { $this->ldap_tls = $ldap_tls; }

	/**
	* Sets Ldap port number
	*
	* @param int $ldap_port ldap port number, normally 389 or 636
	* @return void
	*/
        private function set_port($ldap_port) { $this->ldap_port = $ldap_port; }

	/**
	* Connects to ldap database
	*
	* @param void
	* @return boolean true on success, false otherwise
	*/
	private function connect() {
                $ldap_uri = "";
                if ($this->get_ssl() && (!$this->get_tls())) {
			foreach ($this->get_host() as $host) {
                        	$ldap_uri .= "ldaps://" . $host . ":" . $this->get_port() . " ";
			}
			$this->ldap_resource = ldap_connect($ldap_uri);
                }
                elseif (!$this->get_ssl() && ($this->get_tls())) {
			foreach ($this->get_host() as $host) {
                        	$ldap_uri .= "ldap://" . $host . ":" . $this->get_port() . " ";
			}
			$this->ldap_resource = ldap_connect($ldap_uri);
			ldap_start_tls($this->ldap_resource)
                }
		else {
			foreach ($this->get_host() as $host) {
                                $ldap_uri .= "ldap://" . $host . ":" . $this->get_port() . " ";
                        }
			$this->ldap_resource = ldap_connect($ldap_uri);
		}
		if ($this->get_connection()) {
			return true;
		}
		return false;
        }

}

?>

