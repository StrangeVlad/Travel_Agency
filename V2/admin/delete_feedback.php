<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid feedback ID.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = (int)$_GET['id'];

// Delete the feedback
$stmt = $conn->prepare("DELETE FROM feedbacks WHERE feedback_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin_feedback.php?deleted=1");
} else {
    echo "Error deleting feedback.";
}

$stmt->close();
$conn->close();
?>
