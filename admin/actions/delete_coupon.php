<?php
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

// Check if the user is logged in and has admin privileges
session_start();
if (!isAdmin()) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
  exit;
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get coupon ID
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  // Validate ID
  if (empty($id)) {
    $response['message'] = 'Invalid coupon ID';
    echo json_encode($response);
    exit;
  }

  // Check if the coupon has been used in any bookings
  $stmt = $conn->prepare("SELECT COUNT(*) as usage_count FROM booking_coupons WHERE coupon_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row['usage_count'] > 0) {
    // Coupon has been used, perform soft delete instead
    $stmt = $conn->prepare("UPDATE coupons SET status = 'deleted', deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $response['success'] = true;
      $response['message'] = 'Coupon has been marked as deleted. Cannot be completely removed due to existing usage.';
    } else {
      $response['message'] = 'Error deleting coupon: ' . $conn->error;
    }
  } else {
    // Coupon hasn't been used, proceed with actual deletion
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $response['success'] = true;
      $response['message'] = 'Coupon deleted successfully';
    } else {
      $response['message'] = 'Error deleting coupon: ' . $conn->error;
    }
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid request method';
}

// Return JSON response
echo json_encode($response);
$conn->close();
