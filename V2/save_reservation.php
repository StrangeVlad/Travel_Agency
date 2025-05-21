<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ You must log in first to book a hotel. <a href='register.html'>Create an account</a>");
}

if (!isset($_POST['hotel_id']) || !isset($_POST['destination_id'])) {
    die("❌ Hotel or destination information is missing.");
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("❌ Failed to connect to the database: " . $conn->connect_error);
}

// Receive booking data
$user_id = $_SESSION['user_id'];
$hotel_id = $_POST['hotel_id'];
$phone = $_POST['phone'];
$birthday = $_POST['birthday'];
$passport = $_POST['passport'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$guests = $_POST['guests'];
$room_type = $_POST['roomtype'];
$payment = $_POST['payment'];

// ✅ Automatically fetch destination_id from hotels table
$get_dest = $conn->prepare("SELECT hotel_id, destination_id FROM hotels WHERE hotel_id = ?");
$get_dest->bind_param("i", $hotel_id);
$get_dest->execute();
$dest_result = $get_dest->get_result();

if ($dest_result->num_rows === 0) {
    die("❌ The hotel does not exist.");
}

$row = $dest_result->fetch_assoc();
$destination_id = $row['destination_id'];

// Check if the destination_id exists in the destinations table
$check_destination = $conn->prepare("SELECT destination_id FROM destinations WHERE destination_id = ?");
$check_destination->bind_param("i", $destination_id);
$check_destination->execute();
$result_destination = $check_destination->get_result();

if ($result_destination->num_rows == 0) {
    die("❌ The destination linked to this hotel does not exist in the database.");
}

// ✅ Insert booking into the reservations table
$stmt = $conn->prepare("
    INSERT INTO reservations (
        user_id, hotel_id, phone, birthday, passport,
        checkin_date, checkout_date, guests, room_type,
        payment_method, destination_id, created_at
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

if ($stmt === false) {
    die("❌ Failed to prepare the query.");
}

// Bind data to the query
$stmt->bind_param("iissssssssi", $user_id, $hotel_id, $phone, $birthday, $passport, $checkin, $checkout, $guests, $room_type, $payment, $destination_id);

// Execute the query
if ($stmt->execute()) {
    // Booking confirmed
    echo "<script>alert('✅ Booking confirmed successfully!\\n\\n⚠️ IMPORTANT: Payment must be made within 2 days. Otherwise, your booking will be REFUSED.'); window.location.href='confirmation.php';</script>";
} else {
    die("❌ Booking failed. Please try again.");
}

$stmt->close();
$conn->close();
?>
