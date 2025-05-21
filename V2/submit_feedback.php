<?php
session_start();
include 'db.php'; // Contains your $conn connection

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$message = trim($_POST['feedback_text']);
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO feedbacks (user_id, message, rating, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("isi", $user_id, $message, $rating);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for your feedback!'); </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Feedback message cannot be empty.";
}

$conn->close();
?>
