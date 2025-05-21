<?php
// Database configuration
$host = 'localhost';          // Your database host (usually 'localhost')
$dbname = 'agence_voyage';    // Your database name
$username = 'root';  // Your MySQL username
$password = '';  // Your MySQL password

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally set charset to UTF-8
$conn->set_charset("utf8mb4");
