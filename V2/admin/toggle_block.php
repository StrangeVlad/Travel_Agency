<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 Unauthorized access.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$action = $_GET['action']; // 'block' or 'unblock'

$status = ($action === 'block') ? 1 : 0;

$conn->query("UPDATE users SET is_blocked = $status WHERE id = $id");
$conn->close();

header("Location: manage_users.php");
exit;
