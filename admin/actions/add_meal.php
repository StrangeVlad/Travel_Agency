<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = sanitize_input($_POST['name']);
  $description = sanitize_input($_POST['description']);
  $price = floatval($_POST['price']);

  // Insert into database
  $sql = "INSERT INTO meals (name, description, price) VALUES (?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssd", $name, $description, $price);

  if ($stmt->execute()) {
    header("Location: ../meals.php?success=Meal added successfully");
  } else {
    header("Location: ../meals.php?error=Error adding meal: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../meals.php");
}
$conn->close();
