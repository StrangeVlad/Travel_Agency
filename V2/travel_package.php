<?php
// Initialize session if not already started
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password (empty)
$database = "agence_voyage";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // Redirect with error message
    $errorMsg = urlencode("Database connection failed: " . $conn->connect_error);
    header("Location: custompackages.html?error=1&message=" . $errorMsg);
    exit();
}

// Function to sanitize input data
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Array to store validation errors
    $errors = [];

    // Validate and collect main traveler information
    $fullname = isset($_POST['fullname']) ? sanitizeInput($_POST['fullname']) : '';
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }

    $passport = isset($_POST['passport']) ? sanitizeInput($_POST['passport']) : '';
    if (empty($passport)) {
        $errors[] = "Passport number is required";
    }

    $expire = isset($_POST['expire']) ? sanitizeInput($_POST['expire']) : '';
    if (empty($expire)) {
        $errors[] = "Passport expiry date is required";
    } else {
        // Validate if expiry date is in the future
        $today = date("Y-m-d");
        if ($expire <= $today) {
            $errors[] = "Passport expiry date must be in the future";
        }
    }

    $birthdate = isset($_POST['birthdate']) ? sanitizeInput($_POST['birthdate']) : '';
    if (empty($birthdate)) {
        $errors[] = "Date of birth is required";
    }

    $address = isset($_POST['address']) ? sanitizeInput($_POST['address']) : '';
    if (empty($address)) {
        $errors[] = "Address is required";
    }

    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }

    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    $gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : '';
    if (empty($gender)) {
        $errors[] = "Gender is required";
    }

    $destination = isset($_POST['destination']) ? sanitizeInput($_POST['destination']) : '';
    if (empty($destination)) {
        $errors[] = "Destination is required";
    }

    $departDate = isset($_POST['departDate']) ? sanitizeInput($_POST['departDate']) : '';
    if (empty($departDate)) {
        $errors[] = "Departure date is required";
    } else {
        // Validate if departure date is in the future
        $today = date("Y-m-d");
        if ($departDate < $today) {
            $errors[] = "Departure date must be in the future";
        }
    }

    $returnDate = isset($_POST['returnDate']) ? sanitizeInput($_POST['returnDate']) : '';
    if (empty($returnDate)) {
        $errors[] = "Return date is required";
    } else {
        // Validate if return date is after departure date
        if ($returnDate <= $departDate) {
            $errors[] = "Return date must be after departure date";
        }
    }

    $activities = isset($_POST['activities']) ? sanitizeInput($_POST['activities']) : '';
    if (empty($activities)) {
        $errors[] = "Activities preference is required";
    }

    $transport = isset($_POST['transport']) ? sanitizeInput($_POST['transport']) : '';
    if (empty($transport)) {
        $errors[] = "Transport mode is required";
    }

    $flightClass = isset($_POST['flightClass']) ? sanitizeInput($_POST['flightClass']) : '';
    if (empty($flightClass)) {
        $errors[] = "Flight class is required";
    }

    $hotel = isset($_POST['hotel']) ? sanitizeInput($_POST['hotel']) : '';
    if (empty($hotel)) {
        $errors[] = "Hotel information is required";
    }

    $roomType = isset($_POST['roomType']) ? sanitizeInput($_POST['roomType']) : '';
    if (empty($roomType)) {
        $errors[] = "Room type is required";
    }

    $meal = isset($_POST['meal']) ? sanitizeInput($_POST['meal']) : '';
    if (empty($meal)) {
        $errors[] = "Meal preference is required";
    }

    $visa = isset($_POST['visa']) ? sanitizeInput($_POST['visa']) : '';
    if (empty($visa)) {
        $errors[] = "Visa assistance option is required";
    }

    $guide = isset($_POST['guide']) ? sanitizeInput($_POST['guide']) : '';
    if (empty($guide)) {
        $errors[] = "Tour guide option is required";
    }

    // Adult companion information (optional but validate if provided)
    $adult = isset($_POST['adult']) ? sanitizeInput($_POST['adult']) : 'No';

    // Child information (optional but validate if provided)
    $child = isset($_POST['child']) ? sanitizeInput($_POST['child']) : 'No';

    // Get current user ID if available (Assuming user is logged in and ID is stored in session)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // If there are validation errors, redirect back with error messages
    if (!empty($errors)) {
        $errorMsg = urlencode(implode("<br>", $errors));
        header("Location: custompackages.html?error=1&message=" . $errorMsg);
        exit();
    }

    // Prepare and execute SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO travel_requests (
                fullname, passport, passport_expire, birthdate, address, 
                phone, email, gender, destination, depart_date, 
                return_date, activities, transport, flight_class, hotel, 
                room_type, meal, visa, guide, adult, child, user_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        // Redirect with error message if prepare failed
        $errorMsg = urlencode("Prepare failed: " . $conn->error);
        header("Location: custompackages.html?error=1&message=" . $errorMsg);
        exit();
    }

    $stmt->bind_param(
        "sssssssssssssssssssssi",
        $fullname,
        $passport,
        $expire,
        $birthdate,
        $address,
        $phone,
        $email,
        $gender,
        $destination,
        $departDate,
        $returnDate,
        $activities,
        $transport,
        $flightClass,
        $hotel,
        $roomType,
        $meal,
        $visa,
        $guide,
        $adult,
        $child,
        $user_id
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Success - Redirect to success page or show success message
        header("Location: destination.php?success=1&message=" . urlencode("Travel package request submitted successfully!"));
    } else {
        // Error - Redirect back with error message
        $errorMsg = urlencode("Error submitting request: " . $stmt->error);
        header("Location: custompackages.html?error=1&message=" . $errorMsg);
    }

    // Close statement
    $stmt->close();
} else {
    // If someone tries to access this file directly without submitting the form
    header("Location: custompackages.html");
}

// Close connection
$conn->close();
