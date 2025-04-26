<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = sanitize_input($_POST['title']);
  $destination = sanitize_input($_POST['destination']);
  $description = sanitize_input($_POST['description']);
  $start_date = sanitize_input($_POST['start_date']);
  $end_date = sanitize_input($_POST['end_date']);
  $price = floatval($_POST['price']);
  $total_slots = intval($_POST['total_slots']);
  $available_slots = intval($_POST['available_slots']);

  // Validate dates
  if (strtotime($end_date) < strtotime($start_date)) {
    header("Location: ../packages.php?error=End date must be after start date");
    exit;
  }

  // Validate slots
  if ($available_slots > $total_slots) {
    header("Location: ../packages.php?error=Available slots cannot exceed total slots");
    exit;
  }

  // Insert into database
  $sql = "INSERT INTO packages (title, destination, description, start_date, end_date, price, total_slots, available_slots) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssdii", $title, $destination, $description, $start_date, $end_date, $price, $total_slots, $available_slots);

  if ($stmt->execute()) {
    header("Location: ../packages.php?success=Package added successfully");
  } else {
    header("Location: ../packages.php?error=Error adding package: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../packages.php");
}
$conn->close();
