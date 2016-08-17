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
    $this->connection = new mysqli(Config::read("dbHost"),Config::read("dbUser"),Config::read("dbPass"),Config::read("dbCollection"));
    if(!$this->connection)
      die("Cannot contact database, dying");
    else {
      $this->rows = 0;
      $this->errorMessage = "";
      $this->error = 0;
      $this->result = null;
    }
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


  function toJSON() {
    $response = array();
    $response['data'] = $this->toArray(1);
    $response['length'] = $this->rows;
    $response['inserted'] = $this->insertedId;
    $response['affected'] = $this->affected;
    $response['error'] = $this->error();

    $this->end();
    return json_encode($response);
  }

  function error() {
    $error = array();
    $error['isError'] = $this->error;
    $error['errorMessage'] = $this->errorMessage;
    return $error;
  }

  function end() {
      return $this->connection->close();
  }
}



?>
