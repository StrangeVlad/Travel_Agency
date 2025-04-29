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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? trim($_POST['password']) : '';
  $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
  $role = isset($_POST['role']) ? trim($_POST['role']) : '';
  $status = isset($_POST['status']) ? trim($_POST['status']) : '';

  // Validate inputs
  if (empty($id) || empty($full_name) || empty($email) || empty($username) || empty($role) || empty($status)) {
    $response['message'] = 'All fields are required except password';
    echo json_encode($response);
    exit;
  }

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit;
  }

  // Verify user exists
  $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 0) {
    $response['message'] = 'User not found';
    echo json_encode($response);
    exit;
  }

  // Check if username or email already exists (excluding current user)
  $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
  $stmt->bind_param("ssi", $username, $email, $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $response['message'] = 'Username or email already exists';
    echo json_encode($response);
    exit;
  }

  // Prepare SQL based on whether password is being updated
  if (!empty($password)) {
    // Validate password match
    if ($password !== $confirm_password) {
      $response['message'] = 'Passwords do not match';
      echo json_encode($response);
      exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update user with new password
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, password = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssssssi", $full_name, $email, $username, $hashed_password, $role, $status, $id);
  } else {
    // Update user without changing password
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssssi", $full_name, $email, $username, $role, $status, $id);
  }

  // Execute the statement
  if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'User updated successfully';
  } else {
    $response['message'] = 'Error updating user: ' . $conn->error;
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid request method';
}

// Return JSON response
echo json_encode($response);
$conn->close();
