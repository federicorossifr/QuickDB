<?php
require_once __DIR__ . "/config.php";
class quickDB {
	protected $connection;
	protected $result;
	protected $error;
	protected $errorMessage;
	public $rows;
	public $insertedId;
	public $affected;


	function __construct() {
		try {
			$this->connection = new mysqli(Config::read("dbHost"),Config::read("dbUser"),Config::read("dbPass"),Config::read("dbCollection"));
		} catch (mysqli_sql_exception $exc) {
			$this->errorMessage = $exc->getMessage();
			$this->error = 1;
			echo json_encode($this->error());
			die();
		}
		$this->rows = 0;
		$this->errorMessage = "";
		$this->error = 0;
		$this->result = null;
		$this->insertedId = 0;
		$this->affected = 0;
	}

	function stringEscape(&$string) {
		$this->connection->real_escape_string($string);
		return $string;
	}

	function query($query) {
		$this->result = $this->connection->query($query);
		if($this->connection->error) {
			$this->result = null;
			$this->error = 1;
			$this->errorMessage = $this->connection->error;
			echo json_encode($this->error());
			die();
		} else {
			if(isset($this->result->num_rows))
				$this->rows = $this->result->num_rows;
			$this->insertedId = $this->connection->insert_id;
			$this->affected = $this->connection->affected_rows;
		}
		return $this;
	}

	function toArray($next = 0)  {
		$response = array();
		if(!$this->rows) return;
		while($row = $this->result->fetch_assoc()) {
			array_push($response,$row);
		}

		if(!$next) {
			$this->result->close();
			$this->end();
		}
		return $response;
	}


	function toJSON($next = 0) {
		$response = array();
		$response['data'] = $this->toArray($next);
		$response['length'] = $this->rows;
		$response['insertedId'] = $this->insertedId;
		$response['affected'] = $this->affected;
		$response['error'] = $this->error();
		return json_encode($response);
	}

	function error() {
		$error = array();
		$error['isError'] = $this->error;
		if($this->error) {
			if(Config::read("displayErrorMessages"))
				$error['errorMessage'] = $this->errorMessage;
			else
				$error['errorMessage'] = Config::read("safeErrorMessage");
		}
		return $error;
	}

	function end() {
		return $this->connection->close();
	}

	function reset() {
		$this->rows = 0;
		$this->errorMessage = "";
		$this->error = 0;
		$this->result = null;
	}
}

