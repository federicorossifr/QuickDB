<?php
require_once __DIR__ . "/QuickDB.php";
function indexByKey($array,$key) {
	return array_search($key, array_keys($array));
}

class Model extends QuickDB {
	
	private $name;
	private $fields;
	private $keys;

	function __construct($modelName) {
		parent::__construct();
		$query = "SHOW COLUMNS FROM $modelName";
		$this->name = $modelName;
		$this->keys = array();
		$fields = $this->query($query)->toArray(1);
		foreach ($fields as $value) {
			$this->fields[$value["Field"]] = null;
			if($value["Key"]) array_push($this->keys,$value["Field"]);
		}
		$this->reset();
	}

	function create($fields) {
		$query_0 = "INSERT INTO $this->name(";
		$query_1 = "VALUES (";
		foreach ($fields as $key => $value) {
			$query_0.="$key";
			$this->stringEscape($value);
			$query_1.="'$value'"; //need to escape
			if(indexByKey($fields,$key) != count($fields) - 1) {
				$query_0.= ",";
				$query_1.= ",";
			}
		}
		$query = $query_0 . ") " . $query_1 . ")";
		return $this->query($query)->toJSON(1);
	}

	function read($keys = null) {
		if($keys == null) { //read all
			return $this->query("SELECT * FROM $this->name")->toJSON(1);
		}
		$query = "SELECT * FROM $this->name WHERE";
		foreach ($this->keys as $index => $key) {
			$this->stringEscape($keys[$index]);
			$query.= " $key=$keys[$index]"; //need to escape
			if($index != count($this->keys) - 1) $query.= " AND";
		}

		return $this->query($query)->toJSON(1);
	}

	function update($keys,$fields) {
		$query = "UPDATE $this->name SET";
		foreach($fields as $key => $value) {
			$query.= " $key='$value'";
			if(indexByKey($fields,$key) != count($fields) - 1) {
				$query.=',';
			}
		}
		$query.= " WHERE";
		foreach ($this->keys as $index => $key) {
			$this->stringEscape($keys[$index]);			
			$query.= " $key=$keys[$index]"; //need to escape
			if($index != count($this->keys) - 1) $query.= " AND";
		}
		return $this->query($query)->toJSON(1);
	}

	function delete($keys) {
		$query = "DELETE FROM $this->name WHERE";
		foreach ($this->keys as $index => $key) {
			$this->stringEscape($keys[$index]);			
			$query.= " $key='$keys[$index]'"; //need to escape
			if($index != count($this->keys) - 1) $query.= " AND";
		}
		return $this->query($query)->toJSON(1);
	}

	function where($fields) {
		$query = "SELECT * FROM $this->name WHERE";
		foreach($fields as $key => $value) {
			$this->stringEscape($value);			
			$query.= " $key='$value'";
			if(indexByKey($fields,$key) != count($fields) - 1) {
				$query.=' AND';
			}
		}
		return $this->query($query)->toJSON(1);
	}
}