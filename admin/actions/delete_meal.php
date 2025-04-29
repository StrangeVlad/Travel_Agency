<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  // Check if there are any bookings for this meal
  $sql = "SELECT COUNT(*) as count FROM booking_meals WHERE meal_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete meal as it has associated bookings']);
  } else {
    // Delete meal
    $sql = "DELETE FROM meals WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Failed to delete meal: ' . $conn->error]);
    }
  }

  $stmt->close();
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
$conn->close();
