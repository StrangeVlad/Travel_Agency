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
        SELECT c.*, COUNT(bc.id) as used_count 
        FROM coupons c 
        LEFT JOIN booking_coupons bc ON c.id = bc.coupon_id 
        WHERE c.id = ?
        GROUP BY c.id
    ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Fetch coupon data
    $coupon = $result->fetch_assoc();

    // Format dates for the form
    $coupon['start_date'] = date('Y-m-d', strtotime($coupon['start_date']));
    $coupon['end_date'] = date('Y-m-d', strtotime($coupon['end_date']));

    // Return success with coupon data
    $response['success'] = true;
    $response['coupon'] = $coupon;
  } else {
    $response['message'] = 'Coupon not found';
  }

  $stmt->close();
} else {
  $response['message'] = 'Coupon ID is required';
}

// Return JSON response
echo json_encode($response);
$conn->close();
