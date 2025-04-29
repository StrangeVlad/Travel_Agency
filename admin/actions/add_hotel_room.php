<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $hotel_id = intval($_POST['hotel_id']);
  $room_type = sanitize_input($_POST['room_type']);
  $price_per_night = floatval($_POST['price_per_night']);
  $total_rooms = intval($_POST['total_rooms']);
  $available_rooms = intval($_POST['available_rooms']);

  // Validate hotel exists
  $sql = "SELECT id FROM hotels WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $hotel_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 0) {
    header("Location: ../hotel_rooms.php?error=Invalid hotel selected");
    exit;
  }

  // Validate rooms
  if ($available_rooms > $total_rooms) {
    header("Location: ../hotel_rooms.php?error=Available rooms cannot exceed total rooms");
    exit;
  }

  // Insert into database
  $sql = "INSERT INTO hotel_rooms (hotel_id, room_type, price_per_night, total_rooms, available_rooms) 
            VALUES (?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isiii", $hotel_id, $room_type, $price_per_night, $total_rooms, $available_rooms);

  if ($stmt->execute()) {
    header("Location: ../hotel_rooms.php?success=Room added successfully");
  } else {
    header("Location: ../hotel_rooms.php?error=Error adding room: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../hotel_rooms.php");
}
$conn->close();
