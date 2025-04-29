<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to book a package.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $package_id = filter_input(INPUT_POST, 'package_id', FILTER_SANITIZE_NUMBER_INT);
    $travel_date = filter_input(INPUT_POST, 'travel_date', FILTER_SANITIZE_STRING);
    $num_people = filter_input(INPUT_POST, 'num_people', FILTER_SANITIZE_NUMBER_INT);
    $hotel_id = filter_input(INPUT_POST, 'hotel_id', FILTER_SANITIZE_NUMBER_INT);
    $hotel_room_id = filter_input(INPUT_POST, 'hotel_room_id', FILTER_SANITIZE_NUMBER_INT);
    $nights = filter_input(INPUT_POST, 'nights', FILTER_SANITIZE_NUMBER_INT);
    $special_requests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);
    $meals = isset($_POST['meals']) ? $_POST['meals'] : [];
    
    // Validate required inputs
    if (empty($package_id) || empty($travel_date) || empty($num_people) || empty($hotel_id) || empty($hotel_room_id) || empty($nights)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
        exit();
    }
    
    // Validate package availability
    $package_sql = "SELECT available_slots FROM packages WHERE id = ? AND available_slots >= ?";
    $package_stmt = $conn->prepare($package_sql);
    $package_stmt->bind_param("ii", $package_id, $num_people);
    $package_stmt->execute();
    $package_result = $package_stmt->get_result();
    
    if ($package_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Package is not available for the selected number of people.']);
        exit();
    }
    
    // Validate hotel room availability
    $room_sql = "SELECT available_rooms FROM hotel_rooms WHERE id = ? AND available_rooms >= 1";
    $room_stmt = $conn->prepare($room_sql);
    $room_stmt->bind_param("i", $hotel_room_id);
    $room_stmt->execute();
    $room_result = $room_stmt->get_result();
    
    if ($room_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Selected room is not available.']);
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert booking
        $booking_sql = "INSERT INTO bookings (user_id, package_id, travel_date, num_people, status) VALUES (?, ?, ?, ?, 'booked')";
        $booking_stmt = $conn->prepare($booking_sql);
        $booking_stmt->bind_param("iisi", $user_id, $package_id, $travel_date, $num_people);
        $booking_stmt->execute();
        $booking_id = $conn->insert_id;
        
        // Insert hotel selection
        $hotel_booking_sql = "INSERT INTO booking_hotels (booking_id, hotel_room_id, nights) VALUES (?, ?, ?)";
        $hotel_booking_stmt = $conn->prepare($hotel_booking_sql);
        $hotel_booking_stmt->bind_param("iii", $booking_id, $hotel_room_id, $nights);
        $hotel_booking_stmt->execute();
        
        // Insert meal selections if any
        if (!empty($meals)) {