<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = sanitize_input($_POST['name']);
  $destination = sanitize_input($_POST['destination']);
  $address = sanitize_input($_POST['address']);
  $rating = floatval($_POST['rating']);

  // Validate rating
  if ($rating < 0 || $rating > 5) {
    header("Location: ../hotels.php?error=Rating must be between 0 and 5");
    exit;
  }

  // Insert into database
  $sql = "INSERT INTO hotels (name, destination, address, rating) VALUES (?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssd", $name, $destination, $address, $rating);

  if ($stmt->execute()) {
    header("Location: ../hotels.php?success=Hotel added successfully");
  } else {
    header("Location: ../hotels.php?error=Error adding hotel: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../hotels.php");
}
$conn->close();
