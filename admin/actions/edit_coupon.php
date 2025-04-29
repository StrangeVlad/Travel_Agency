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
  $code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';
  $description = isset($_POST['description']) ? trim($_POST['description']) : '';
  $discount_type = isset($_POST['discount_type']) ? trim($_POST['discount_type']) : '';
  $discount_value = isset($_POST['discount_value']) ? floatval($_POST['discount_value']) : 0;
  $min_purchase = isset($_POST['min_purchase']) ? floatval($_POST['min_purchase']) : 0;
  $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
  $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
  $max_uses = isset($_POST['max_uses']) ? intval($_POST['max_uses']) : 0;
  $status = isset($_POST['status']) ? trim($_POST['status']) : 'active';

  // Validate required fields
  if (
    empty($id) || empty($code) || empty($description) || empty($discount_type) ||
    empty($discount_value) || empty($start_date) || empty($end_date)
  ) {
    $response['message'] = 'All required fields must be filled';
    echo json_encode($response);
    exit;
  }

  // Validate discount type
  if ($discount_type != 'percentage' && $discount_type != 'fixed') {
    $response['message'] = 'Invalid discount type';
    echo json_encode($response);
    exit;
  }

  // Validate discount value
  if ($discount_value <= 0) {
    $response['message'] = 'Discount value must be greater than zero';
    echo json_encode($response);
    exit;
  }

  // For percentage discounts, ensure value is <= 100
  if ($discount_type == 'percentage' && $discount_value > 100) {
    $response['message'] = 'Percentage discount cannot exceed 100%';
    echo json_encode($response);
    exit;
  }

  // Validate dates
  $startDateTime = new DateTime($start_date);
  $endDateTime = new DateTime($end_date);

  if ($startDateTime > $endDateTime) {
    $response['message'] = 'End date must be after start date';
    echo json_encode($response);
    exit;
  }

  // Format dates for database
  $start_date_formatted = $startDateTime->format('Y-m-d');
  $end_date_formatted = $endDateTime->format('Y-m-d');

  // Check if coupon exists
  $stmt = $conn->prepare("SELECT id FROM coupons WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 0) {
    $response['message'] = 'Coupon not found';
    echo json_encode($response);
    exit;
  }

  // Check if coupon code already exists (exclude current coupon)
  $stmt = $conn->prepare("SELECT id FROM coupons WHERE code = ? AND id != ?");
  $stmt->bind_param("si", $code, $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $response['message'] = 'Coupon code already exists';
    echo json_encode($response);
    exit;
  }

  // Update coupon in database
  $stmt = $conn->prepare("
        UPDATE coupons 
        SET code = ?, 
            description = ?, 
            discount_type = ?, 
            discount_value = ?, 
            min_purchase = ?, 
            start_date = ?, 
            end_date = ?, 
            max_uses = ?, 
            status = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
  $stmt->bind_param("sssddssisi", $code, $description, $discount_type, $discount_value, $min_purchase, $start_date_formatted, $end_date_formatted, $max_uses, $status, $id);

  if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Coupon updated successfully';
  } else {
    $response['message'] = 'Error updating coupon: ' . $conn->error;
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid request method';
}

// Return JSON response
echo json_encode($response);
$conn->close();
