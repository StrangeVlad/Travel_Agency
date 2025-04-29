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

// Check if id parameter is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $id = intval($_GET['id']);

  // Prepare and execute the query
  $stmt = $conn->prepare("SELECT id, full_name, email, username, role, status FROM users WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();

    // Return success with user data
    $response['success'] = true;
    $response['user'] = $user;
  } else {
    $response['message'] = 'User not found';
  }

  $stmt->close();
} else {
  $response['message'] = 'User ID is required';
}

// Return JSON response
echo json_encode($response);
$conn->close();
