<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 Unauthorized.");
}

$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name = $_POST['name'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $destination_id = $_POST['destination_id'];
    $other_services = $_POST['other_services'];
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : '';
    $full_services = trim($services . ', ' . $other_services, ', ');

    // Handle image upload
    $uploadedPath = '';
    if (!empty($_FILES['image']['name'][0])) {
        $image = $_FILES['image']['name'][0];
        $tmp = $_FILES['image']['tmp_name'][0];
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uniqueName = uniqid("hotel_", true) . "." . pathinfo($image, PATHINFO_EXTENSION);
        $path = $uploadDir . basename($uniqueName);
        //  Move uploaded file to target folder
        if (move_uploaded_file($tmp, $path)) {
            $uploadedPath = $path;
        } else {
            die("❌ Failed to upload image.");
        }
    }

    // Prepare & execute
    $stmt = $pdo->prepare("INSERT INTO hotels (destination_id, name, location, price, rating, image, description)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$destination_id, $name, $location, $price, $rating, $uploadedPath, $full_services]);
    //  Redirect to hotel listing page on success
    if ($success) {
        header("Location: view_hotels.php?success=1");
        exit;
    } else {
        die(" Failed to insert hotel.");
    }

} catch (PDOException $e) {
        //  Handle any database-related error
    die(" Database error: " . $e->getMessage());
}
?>
