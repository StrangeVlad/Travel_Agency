<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = intval($_POST['id']);
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

  // Update database
  $sql = "UPDATE hotel_rooms SET hotel_id=?, room_type=?, price_per_night=?, 
            total_rooms=?, available_rooms=? WHERE id=?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isdiii", $hotel_id, $room_type, $price_per_night, $total_rooms, $available_rooms, $id);

  if ($stmt->execute()) {
    header("Location: ../hotel_rooms.php?success=Room updated successfully");
  } else {
    header("Location: ../hotel_rooms.php?error=Error updating room: " . $conn->error);
  }

  $stmt->close();
} else {
  header("Location: ../hotel_rooms.php");
}
$conn->close();
