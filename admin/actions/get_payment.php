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
  $stmt = $conn->prepare("
        SELECT p.*, b.id as booking_id, u.full_name as customer_name 
        FROM payments p 
        LEFT JOIN bookings b ON p.booking_id = b.id 
        LEFT JOIN users u ON b.user_id = u.id 
        WHERE p.id = ?
    ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Fetch payment data
    $payment = $result->fetch_assoc();

    // Format date
    $payment['payment_date'] = date('M d, Y H:i:s', strtotime($payment['payment_date']));

    // Return success with payment data
    $response['success'] = true;
    $response['payment'] = $payment;
  } else {
    $response['message'] = 'Payment not found';
  }

  $stmt->close();
} else {
  $response['message'] = 'Payment ID is required';
}

// Return JSON response
echo json_encode($response);
$conn->close();
