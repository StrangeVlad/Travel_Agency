<?php
include '../includes/db_connection.php';
include '../includes/functions.php';
check_admin_login();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $sql = "SELECT * FROM meals WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $meal = $result->fetch_assoc();
    echo json_encode($meal);
  } else {
    echo json_encode(['error' => 'Meal not found']);
  }

  $stmt->close();
} else {
  echo json_encode(['error' => 'ID parameter is required']);
}
$conn->close();
