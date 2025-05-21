<?php
session_start();
if (!isset($_SESSION["admin"])) {
    http_response_code(403);
    exit("Unauthorized");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) die("Connection failed");

// Mark all unseen users as seen
$conn->query("UPDATE users SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");

echo "Notifications marked as seen";
?>
