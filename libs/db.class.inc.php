<?php
/**
* db class interacts with mysql database using PDO
*
*/

namespace IGBIllinois;

/**
* db class interacts with mysql database using PDO
*
* Provides all necessary functions to connect and run sql queries on the database
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
class db {

	////////////////Private Variables//////////

	/** @var \PDO PDO database object */
	private $link; //mysql database link
	/** @var string hostname or IP of mysql server */
	private $host;
	/** @var string name of mysql database */
	private $database;
	/** @var string username to connect to the database */
	private $username;
	/** @var string password to connect to the database */
	private $password;
	/** @var bool enable or disable ssl connection. defaults to false */
	private $ssl = false;
 
	////////////////Public Functions///////////

        /**
        * Creates mysql database object
        *
        * @param string @host hostname or IP address of mysql server
        * @param string @database database name
        * @param string @username username to connect to database
        * @param string @password password to connect to database
        * @param int @port mysql port number. defaults to 3306
        * @param bool @ssl use ssl
        *
        * @throws \PDOException
        *
        * @return \IGBIllinois\db
        */

	public function __construct($host,$database,$username,$password,$ssl = false) {
		try {
			$this->open($host,$database,$username,$password,$ssl);
		}
		catch(\PDOException $e) {
                        throw $e;
                }
	


	}

	/**
	* Destroys db object.  Closes mysql database connection
	*
	* @param void
	*
	* @return void
	*/
	public function __destruct() {
		$this->close();

	}

	/**
	* Opens mysql database connection
	*
	* @param string @host hostname or IP address of mysql server
	* @param string @database database name
	* @param string @username username to connect to database
	* @param string @password password to connect to database
	* @param int @port mysql port number. defaults to 3306
	* @param bool @ssl use ssl
	*
	* @throws \PDOException
	*
	* @return void
	*/
	private function open($host,$database,$username,$password,$port = 3306,$ssl = false) {
		//Connects to database.
		try {
			$params = array();
			$params[\PDO::ATTR_PERSISTENT] =false;
			if ($ssl) {
				$params[\PDO::MYSQL_ATTR_SSL_CA] = '/dev/null';
				$params[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
			}
			$this->link = new \PDO("mysql:host=$host;dbname=$database",$username,$password,$params);
			$this->link->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING);
			$this->host = $host;
			$this->database = $database;
			$this->username = $username;
			$this->password = $password;
			$this->ssl = $ssl;
		}
		catch(\PDOException $e) {
			throw $e;
		}

	}

	/**
	* Closes mysql database connection
	*
	* @param void
	*
	* @return void
	*
	*/
	public function close() {
		$this->link = null;
	}

	/**
	* Runs insert query on database
	*
	* @param string $sql sql query to run on the database
	* @param string[] $parameters an array of PDO prepared parameters
	*
	* @throws \PDOException
	*
	* @return int insert id returned from database. 0 otherwise
	*/
	public function insert_query($sql,$parameters=array()) {
		try {
			$result = $this->link->prepare($sql);
			$retVal = $result->execute($parameters);
			if ($retVal === false) {
			}
			return $this->link->lastInsertId();
		}
		catch(\PDOException $e) {
			throw $e;
		}
		return 0;
	}

	/**
	* Builds insert query using associative array
	*
	* @param string $table table to insert data into
	* @param string[] $data associative array with key being table colume and value being the data
	* 
	* @throws \PDOException
	*
	* @return int insert id returned from database. 0 otherwise
	*/
	public function build_insert($table,$data) {
		$sql = "INSERT INTO " . $table;
		$values_sql = "VALUES(";
		$columns_sql = "(";
		$args = array();
		$count = 0;
		foreach ($data as $key=>$value) {
			if ($count == 0) {
				$columns_sql .= $key;
				$values_sql .= ":".$key;
			}
			else {
				$columns_sql .= "," . $key;
				$values_sql .= ",:".$key;
			}
			$args[':'.$key]=$value;

			$count++;
		}
		$values_sql .= ")";
		$columns_sql .= ")";
		$sql = $sql . $columns_sql . " " . $values_sql;
		try {
			return $this->insert_query($sql,$args);
		}
		catch (\PDOException $e) {
			throw $e;
			return 0;
		}
	}

	/**
	* Runs a non select or insert query on database
	*
	* @param string $sql sql string to run
	* @param string[] $parameters an array of PDO prepared parameters
	*
	* @throws \PDOException
	*
	* @return bool true on success, false otherwise
	*/
	public function non_select_query($sql,$parameters=array()) {
		try {
			$result = $this->link->prepare($sql);
			$retval = $result->execute($parameters);
			return $retval;
		}
                catch(\PDOException $e) {
			throw $e;
                        return false;
                }
	}

	/**
	* Runs sql query for SELECT queries
	*
	* @param string $sql sql query to run
	* @param string[] $parameters an array of PDO prepared parameters
	*
	* @throws \PDOException
	*
	* @return string[] an associative array of results
	*/
	public function query($sql,$parameters=array()) {
		try {
			$result = $this->link->prepare($sql);
			$result->execute($parameters);
			return $result->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch(\PDOException $e) {
			throw $e;
			return array();
		}
	}

        /**
        * Retrieves mysql database PDO object
        *
        * @param void
        *
        * @return \PDO Returns PDO object with the current connection
        */
	public function get_link() {
		return $this->link;
	}

	/**
	* Tests connection to mysql database
	* 
	* @param void
	*
	* @return boolean Returns true on success, false otherwise
	*
	*/
	public function ping() {
		try {
			if ($this->link->getAttribute(\PDO::ATTR_CONNECTION_STATUS)) {
				return true;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return false;

	}

	/**
	* Runs mysql transaction
	*
	* @param string $sql sql string to run
	* @param string[] $parameters an array of PDO prepared parameters
	*
	* @throws \PDOException
	*
	* @returns int number of rows affected
	*/
	public function transaction($sql,$parameters) {
		try {
			$this->link->beginTransaction();
			$result = $this->link->prepare($sql);
			$result->execute($parameters);
			$this->link->commit();
			return $this->link->rowCount();
		}
		catch(\PDOException $e) {
			throw $e;
			return 0;
		}

	}

	/**
	* Runs update query on table
	*
	* @param string $table name of table
	* @param string[] $parameters an array of PDO prepared parameters
	* @param string $where_key column name to use in where clause
	* @param string $where_value value column needs to be equal to
	*
	* @throws \PDOException
	*
	* @return bool returns true on success, false otherwise 
	*/
	public function update($table,$parameters,$where_key,$where_value) {
                try {

                        $sql = "UPDATE `" . $table . "` SET ";

                        $count = count($parameters);
                        $i = 1;
                        foreach ($parameters as $key=>$value) {
                                if ($i == $count) {
                                        $sql .= $key . "= :" . $key . " ";;
                                }
                                else {
                                        $sql .= $key . "= :" . $key . ", ";
                                }

                                $i++;
                        }
                        $sql .= "WHERE " . $where_key . "='" . $where_value . "' LIMIT 1";
                        $statement = $this->link->prepare($sql);
                        foreach ($data as $key=>$value) {
                                $statement->bindValue(":" . $key,$value);
                        }
                        $result = $statement->execute();
                        return $result;
                }
                catch(\PDOException $e) {
                        throw $e;
			return false;
                }


        }

	/**
	* Retrieves version number of mysql server.
	*
	* An example is 5.5.5-10.3.17-MariaDB
	*
	* @param void
	*
	* @return string
	*/
	public function get_version() {
		try {
			return $this->link->getAttribute(\PDO::ATTR_SERVER_VERSION);
		}
		catch(\PDOException $e) {
			echo $e->getMessage();
		}

	}

}
?>
