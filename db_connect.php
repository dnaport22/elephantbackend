<?php

/**
 * Class dbConnect implements a wrapper around database connections.
 *
 * This class reads the configuration from a config.ini
 *
 * @example The config.ini must look like
 * [db_credentials]
 * hostname = localhost
 * username = sususer
 * password = suspassword
 * dbname = susdb
 */
class dbConnect
{
	/**
	 * Connection to the database.
	 *
	 * @var PDO
	 */
	private $connection;

	/**
	 * Database connection parameters.
	 * @var string
	 */
	private $hostname = NULL;
  private $username = NULL;
  private $password = NULL;
  private $dbname = NULL;

	/**
	 * Loads the database settings.
	 *
	 * @param array $data
	 *   database settings array.
	 */
	public function load($data){
		if ($data['db_credentials']) {
			$credentials = &$data['db_credentials'];
			$this->hostname = $credentials['hostname'];
			$this->username = $credentials['username'];
			$this->password = $credentials['password'];
			$this->dbname = $credentials['dbname'];
		}
	}

	/**
	 * dbConnect constructor.
	 */
	public function __construct() {
		$this->load(parse_ini_file("../config/config.ini", TRUE));
		$this->connection = new PDO("mysql:host=$this->hostname;dbname=$this->dbname", $this->username, $this->password);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Executes a query.
	 *
	 * @param string $query
	 *   Query to execute.
	 *
	 * @param array $parameters
	 *   Parameters to be passed to the PDOStatement.
	 *
	 * @return PDOStatement
	 */
	public function query($query, $parameters) {
		$stmt = $this->connection->prepare($query);
		$stmt->execute($parameters);
		return $stmt;
	}

	public function queryCast($query, $parameters) {
		$stmt = $this->connection->prepare($query);
		foreach ($parameters as $key => $value) {
	    if (is_integer($value)) {
				$stmt->bindValue($key, $value, PDO::PARAM_INT);
			}
			if (is_string($value)) {
				$stmt->bindValue($key, $value, PDO::PARAM_STR);
			}
		}
		$stmt->execute();
		return $stmt;
	}
}

$mysql_db = new dbConnect();
