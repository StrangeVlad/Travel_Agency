<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = intval($_POST['id']);
  $name = sanitize_input($_POST['name']);
  $description = sanitize_input($_POST['description']);
  $price = floatval($_POST['price']);

  // Update database
  $sql = "UPDATE meals SET name=?, description=?, price=? WHERE id=?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssdi", $name, $description, $price, $id);

  if ($stmt->execute()) {
    header("Location: ../meals.php?success=Meal updated successfully");
  } else {
    header("Location: ../meals.php?error=Error updating meal: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../meals.php");
}
$conn->close();
