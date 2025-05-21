<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "agence_voyage");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and fetch form data
$user_id = $_SESSION["user_id"];
$hotel_id = intval($_POST["hotel_id"]);
$checkin = $_POST["checkin_date"];
$checkout = $_POST["checkout_date"];
$guests = intval($_POST["guests"]);
$room_type = $conn->real_escape_string($_POST["room_type"]);
$email = $conn->real_escape_string($_POST["email"]);
$payment_method = $conn->real_escape_string($_POST["payment_method"]);

// Insert query with email and payment method
$sql = "INSERT INTO reservations (user_id, hotel_id, checkin_date, checkout_date, guests, room_type, email, payment_method)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the parameters (adjust the types: i for int, s for string)
$stmt->bind_param("iississs", $user_id, $hotel_id, $checkin, $checkout, $guests, $room_type, $email, $payment_method);

// Execute the statement and check for success
if ($stmt->execute()) {
    // Redirect to the thank you page with the hotel ID
    header("Location: thank_you.php?hotel=$hotel_id");
    exit();
} else {
    echo "❌ Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
