<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 Unauthorized access.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_POST['id']);
$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$email = $conn->real_escape_string($_POST['email']);

// ✅ Check if the email exists for another user
$check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$check->bind_param("si", $email, $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // ❌ Email already in use
    echo "❌ Email is already used by another account.";
    exit;
} else {
    // ✅ Safe to update
    $update = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $update->bind_param("sssi", $first_name, $last_name, $email, $id);
    if ($update->execute()) {
        header("Location: manage_users.php?success=1");
        exit;
    } else {
        echo "❌ Failed to update user.";
    }
}

$conn->close();
?>
