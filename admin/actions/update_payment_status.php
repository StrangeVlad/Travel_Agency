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
  $id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
  $status = isset($_POST['status']) ? trim($_POST['status']) : '';
  $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

  // Validate required fields
  if (empty($id) || empty($status)) {
    $response['message'] = 'Payment ID and status are required';
    echo json_encode($response);
    exit;
  }

  // Validate status value
  $valid_statuses = ['completed', 'pending', 'cancelled', 'refunded'];
  if (!in_array($status, $valid_statuses)) {
    $response['message'] = 'Invalid status value';
    echo json_encode($response);
    exit;
  }

  // Update payment status in database
  $stmt = $conn->prepare("UPDATE payments SET status = ?, notes = CONCAT(IFNULL(notes, ''), ?, '\n'), updated_at = NOW() WHERE id = ?");
  $notes_with_timestamp = empty($notes) ? '' : "\n" . date('Y-m-d H:i:s') . " - Status changed to " . ucfirst($status) . ": " . $notes;
  $stmt->bind_param("ssi", $status, $notes_with_timestamp, $id);

  if ($stmt->execute()) {
    // Handle additional actions based on status change
    if ($status == 'completed') {
      // Update the related booking status if payment is completed
      $stmt = $conn->prepare("
                UPDATE bookings b 
                JOIN payments p ON b.id = p.booking_id 
                SET b.payment_status = 'paid', b.updated_at = NOW() 
                WHERE p.id = ?
            ");
      $stmt->bind_param("i", $id);
      $stmt->execute();
    } elseif ($status == 'refunded') {
      // Update the related booking status if payment is refunded
      $stmt = $conn->prepare("
                UPDATE bookings b 
                JOIN payments p ON b.id = p.booking_id 
                SET b.payment_status = 'refunded', b.updated_at = NOW() 
                WHERE p.id = ?
            ");
      $stmt->bind_param("i", $id);
      $stmt->execute();
    }

    $response['success'] = true;
    $response['message'] = 'Payment status updated successfully';
  } else {
    $response['message'] = 'Error updating payment status: ' . $conn->error;
  }

  $stmt->close();
} else {
  $response['message'] = 'Invalid request method';
}

// Return JSON response
echo json_encode($response);
$conn->close();
