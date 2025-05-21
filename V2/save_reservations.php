<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // Store current URL to redirect back after login
  $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'destinations.php';
  header("Location: login.php");
  exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agence_voyage";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get form data
  $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;
  $destination_id = isset($_POST['destination_id']) ? intval($_POST['destination_id']) : 0;
  $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
  $birthday = isset($_POST['birthday']) ? $conn->real_escape_string($_POST['birthday']) : '';
  $passport = isset($_POST['passport']) ? $conn->real_escape_string($_POST['passport']) : '';
  $checkin = isset($_POST['checkin']) ? $conn->real_escape_string($_POST['checkin']) : '';
  $checkout = isset($_POST['checkout']) ? $conn->real_escape_string($_POST['checkout']) : '';
  $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 0;
  $room_type = $_POST['roomtype'];

  $payment = isset($_POST['payment']) ? $conn->real_escape_string($_POST['payment']) : '';

  // Get user ID if logged in, otherwise set to NULL
  $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

  // Validate inputs
  $errors = [];

  if ($hotel_id <= 0) {
    $errors[] = "Invalid hotel selection.";
  }

  if ($destination_id <= 0) {
    $errors[] = "Invalid destination selection.";
  }

  if (empty($phone)) {
    $errors[] = "Phone number is required.";
  }

  if (empty($birthday)) {
    $errors[] = "Date of birth is required.";
  }

  if (empty($passport)) {
    $errors[] = "Passport number is required.";
  }

  if (empty($checkin)) {
    $errors[] = "Check-in date is required.";
  }

  if (empty($checkout)) {
    $errors[] = "Check-out date is required.";
  }

  if ($guests <= 0) {
    $errors[] = "Number of guests must be at least 1.";
  }

  if (empty($room_type)) {
    $errors[] = "Room type is required.";
  }

  if (empty($payment)) {
    $errors[] = "Payment method is required.";
  }

  // Check if check-out is after check-in
  if (!empty($checkin) && !empty($checkout)) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);

    if ($checkout_date <= $checkin_date) {
      $errors[] = "Check-out date must be after check-in date.";
    }
  }

  // If there are no errors, save the reservation
  if (empty($errors)) {
    // Prepare SQL statement for inserting reservation
    $sql = "INSERT INTO reservations (user_id, hotel_id, checkin_date, checkout_date, guests, room_type, payment_method, phone, birthday, passport, destination_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssssssi", $user_id, $hotel_id, $checkin, $checkout, $guests, $room_type, $payment, $phone, $birthday, $passport, $destination_id);

    if ($stmt->execute()) {
      // Reservation saved successfully
      $_SESSION['success_message'] = "Your reservation has been confirmed!";

      // Redirect back to destinations page
      echo "<script>alert('✅ Booking confirmed successfully!\\n\\n⚠️ IMPORTANT: Payment must be made within 2 days. Otherwise, your booking will be REFUSED.'); window.location.href='confirmation.php';</script>";
      exit();
    } else {
      // Error saving reservation
      $_SESSION['error_message'] = "Error saving reservation: " . $conn->error;

      // Redirect back to destinations page
      header("Location: destination.php?destination_id=" . $destination_id);
      exit();
    }
  } else {
    // There were validation errors
    $_SESSION['error_message'] = "Please fix the following errors:<br>" . implode("<br>", $errors);

    // Redirect back to destinations page
    header("Location: destination.php?destination_id=" . $destination_id);
    exit();
  }
} else {
  // If not a POST request, redirect to home page
  header("Location: agence.html");
  exit();
}

// Close the database connection
$conn->close();
