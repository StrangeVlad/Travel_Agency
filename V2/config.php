<?php
$servername = "localhost";  // or 127.0.0.1
$username = "root";         // default for XAMPP
$password = "";             // default is empty
$dbname = "agence_voyage";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
