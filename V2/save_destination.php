<?php
session_start();

//  Check if the admin is logged in
if (!isset($_SESSION["admin"])) {
    die(" You are not authorized to access this page.");
}

//  Connect to the database
$conn = new mysqli("localhost", "root", "", "agence_voyage");

//  Check connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Check if required POST data is submitted
if (isset($_POST['country_name'], $_POST['description'], $_POST['tour_type'])) {
    $country_name = $_POST['country_name'];
    $description = $_POST['description'];
    $tour_type = $_POST['tour_type'];

    //  Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';

        // 🔧 Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        //  Rename and move the uploaded image to the uploads folder
        $image = $uploadDir . time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    //  Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO destinations (country_name, description, image, tour_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $country_name, $description, $image, $tour_type);

    //  Check execution result
    if ($stmt->execute()) {
        echo "Destination added successfully! <br><a href='dashboard.php'>⬅ Back to Dashboard</a>";
    } else {
        echo " Error: " . $stmt->error;
    }

    $stmt->close(); //  Close the statement
} else {
    echo " Incomplete form data.";
}

$conn->close(); //  Close the database connection to free up resources
?>
