<?php

// Database connection details
$host = 'localhost'; // Database host
$db_name = 'thegallerycafe'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password
$charset = 'utf8mb4'; // Character set

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

try {
    // Create a PDO instance
    $conn = new PDO($dsn, $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}

?>
