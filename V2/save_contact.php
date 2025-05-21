<?php
session_start(); // Start the session to check user login

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to book the offer. <a href='register.html'>Create an account</a>");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$destination = $_POST['destination'] ?? '';
$delivery_date = date('Y-m-d'); // Booking date
$expiration_date = date('Y-m-d', strtotime('+2 days')); // Two days from booking
$created_at = date('Y-m-d H:i:s');
$status = 'pending'; // Initial status: waiting for payment

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO contact_requests (name, email, destination, created_at, delivery_date, expiration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    die("❌ Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("sssssss", $name, $email, $destination, $created_at, $delivery_date, $expiration_date, $status);

// Execute and redirect
if ($stmt->execute()) {
    header("Location: thanks.php?name=" . urlencode($name) . "&email=" . urlencode($email) . "&destination=" . urlencode($destination) . "&delivery_date=" . urlencode($delivery_date) . "&expiration_date=" . urlencode($expiration_date));
    exit;
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
