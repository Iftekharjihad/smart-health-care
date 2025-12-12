<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change to your database username
define('DB_PASS', ''); // Change to your database password
define('DB_NAME', 'smart_healthcare');

// Create connection
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to UTF-8
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}

// Test connection
function testConnection() {
    $conn = getConnection();
    if ($conn) {
        echo "Database connected successfully!";
        $conn->close();
    }
}

// Only test if explicitly called
if (isset($_GET['test_db'])) {
    testConnection();
}
?>