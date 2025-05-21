<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // First, delete user's reservations
    $conn->query("DELETE FROM reservations WHERE user_id = $user_id");

    // Then, delete user
    $conn->query("DELETE FROM users WHERE id = $user_id");

    header("Location: manage_users.php");
    exit();
}
?>
