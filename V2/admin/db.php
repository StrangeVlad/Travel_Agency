<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "agence_voyage"; // غيّره إذا كان اسم قاعدة البيانات مختلفاً

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
