<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_system');

// Create database connection
function connectDB() {
    try {
        // First connect without database selection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error creating database: " . $conn->error);
        }
        
        // Select the database
        if (!$conn->select_db(DB_NAME)) {
            throw new Exception("Error selecting database: " . $conn->error);
        }
        
        return $conn;
        
    } catch (Exception $e) {
        // Check if MySQL is running
        if (!@fsockopen(DB_HOST, 3306, $errno, $errstr, 5)) {
            die("Error: Cannot connect to MySQL. Please ensure MySQL is running in XAMPP.<br>
                 1. Open XAMPP Control Panel<br>
                 2. Start MySQL service<br>
                 3. Try again");
        }
        
        die("Database Error: " . $e->getMessage());
    }
}

