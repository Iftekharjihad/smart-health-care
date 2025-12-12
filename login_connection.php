<?php
// db_connection.php
class Database {
    private $host = "localhost";
    private $username = "root"; // Change as needed
    private $password = ""; // Change as needed
    private $database = "smart_healthcare";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to UTF-8
            $this->conn->set_charset("utf8");
            
        } catch(Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
        
        return $this->conn;
    }
}

// Create global database instance
$database = new Database();
$conn = $database->getConnection();
?>