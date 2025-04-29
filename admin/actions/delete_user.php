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
  // Get user ID
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  // Validate ID
  if (empty($id)) {
    $response['message'] = 'Invalid user ID';
    echo json_encode($response);
    exit;
  }

  // Check if it's the last admin user
  $stmt = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin' AND id != ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  // Check if trying to delete the last admin
  if ($row['admin_count'] == 0) {
    // Check if the user we're deleting is an admin
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['role'] == 'admin') {
      $response['message'] = 'Cannot delete the last admin user';
      echo json_encode($response);
      exit;
    }
  }

  // Check if user has associated data that should prevent deletion
  // In a travel agency system, we might want to check for bookings, transactions, etc.
  $stmt = $conn->prepare("SELECT COUNT(*) as booking_count FROM bookings WHERE user_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row['booking_count'] > 0) {
    // User has bookings, consider using soft delete instead
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive', deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $response['success'] = true;
      $response['message'] = 'User has been deactivated. Cannot be fully deleted due to associated bookings.';
    } else {
      $response['message'] = 'Error deactivating user: ' . $conn->error;
    }
  } else {
    // No bookings, proceed with actual deletion
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      $response['success'] = true;
      $response['message'] = 'User deleted successfully';
    } else {
      $response['message'] = 'Error deleting user: ' . $conn->error;
    }
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid request method';
}

// Return JSON response
echo json_encode($response);
$conn->close();
