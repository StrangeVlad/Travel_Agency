<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = intval($_POST['id']);
  $name = sanitize_input($_POST['name']);
  $destination = sanitize_input($_POST['destination']);
  $address = sanitize_input($_POST['address']);
  $rating = floatval($_POST['rating']);

  // Validate rating
  if ($rating < 0 || $rating > 5) {
    header("Location: ../hotels.php?error=Rating must be between 0 and 5");
    exit;
  }

  // Update database
  $sql = "UPDATE hotels SET name=?, destination=?, address=?, rating=? WHERE id=?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssdi", $name, $destination, $address, $rating, $id);

  if ($stmt->execute()) {
    header("Location: ../hotels.php?success=Hotel updated successfully");
  } else {
    header("Location: ../hotels.php?error=Error updating hotel: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../hotels.php");
}
$conn->close();
