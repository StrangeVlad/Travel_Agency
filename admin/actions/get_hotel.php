<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $sql = "SELECT * FROM hotels WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $hotel = $result->fetch_assoc();
    echo json_encode($hotel);
  } else {
    echo json_encode(['error' => 'Hotel not found']);
  }

  $stmt->close();
} else {
  echo json_encode(['error' => 'ID parameter is required']);
}
$conn->close();
