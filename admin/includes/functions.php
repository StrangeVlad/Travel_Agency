<?php
// Sanitize input function
function sanitize_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Function to check if admin is logged in
function check_admin_login()
{
  session_start();
  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
  }
} // Function to check if user is admin (returns true/false)
function isAdmin()
{
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}
