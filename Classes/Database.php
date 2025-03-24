<?php
class Database {
    private $servername = "localhost"; // Your database server name (usually localhost)
    private $username = "root"; // Your database username 
    private $password = ""; // Your database password
    private $dbname = "bakery_oop"; // Your database name

    public $conn; // Connection variable to be accessed by child classes
    public $error = "";

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Unable to connect to database: " . $this->dbname . " : " . $this->conn->connect_error);
        }
        
    }
    public function getError() { 
      return $this->conn->error;
    }
    public function escapeString($string) {
      return $this->conn->real_escape_string($string); 
  }
}
?>