<?php
// actions/login_action.php

// Don’t ever output PHP errors to the client — we’ll log them instead
ini_set('display_errors', 0);
error_reporting(0);

// Force JSON response
header('Content-Type: application/json; charset=utf-8');

// Start session
session_start();

// Include your database connection — use __DIR__ to get the correct path
require_once __DIR__ . '/../../res/db_connection.php';

// Prepare default response
$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize inputs
  $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $response['message'] = 'Please enter both email and password.';
  } else {
    // Lookup user
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password']) || $password === $user['password']) {
        // Success: set session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role']       = $user['role'];

        $response = ['success' => true];
      } else {
        $response['message'] = 'Invalid password.';
      }
    } else {
      $response['message'] = 'No account found with that email.';
    }
    $stmt->close();
  }
}

// Make sure nothing else is echoed
echo json_encode($response);
exit;
