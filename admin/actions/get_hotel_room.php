<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $sql = "SELECT * FROM hotel_rooms WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $room = $result->fetch_assoc();
    echo json_encode($room);
  } else {
    echo json_encode(['error' => 'Room not found']);
  }

  $stmt->close();
} else {
  echo json_encode(['error' => 'ID parameter is required']);
}
$conn->close();
