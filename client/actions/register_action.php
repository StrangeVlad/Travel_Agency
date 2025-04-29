<?php
// Turn off output errors and set content type
ini_set('display_errors', 0);
error_reporting(0);

// Always send JSON
header('Content-Type: application/json; charset=utf-8');

// Start session and include necessary files
session_start();
require_once '../includes/db_connection.php'; // Adjust path if needed
require_once '../includes/functions.php';     // Adjust path if needed

$response = ['success' => false, 'message' => 'Registration failed.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get and sanitize inputs
  $name = sanitize_input($_POST['name'] ?? '');
  $email = sanitize_input($_POST['email'] ?? '');
  $phone = sanitize_input($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';

  // Validate
  if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    $response['message'] = 'All fields are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format.';
  } elseif (strlen($password) < 8) {
    $response['message'] = 'Password must be at least 8 characters.';
  } else {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $response['message'] = 'Email is already registered.';
    } else {
      // Insert new user
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

      if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Registered successfully.'];
      } else {
        $response['message'] = 'Database error. Please try again.';
      }
    }
    $stmt->close();
  }
}

// Always return JSON
echo json_encode($response);
exit;

// Helper sanitize function
function sanitize_input($data)
{
  return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
