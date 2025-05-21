<?php
session_start();

//  Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Validate form input
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["request_id"])) {
    $requestId = $_POST["request_id"];

    // Database connection
    $host = 'localhost';
    $db   = 'agence_voyage';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //  Reset admin decision
        $stmt = $pdo->prepare("UPDATE travel_requests SET admin_status = NULL WHERE id = :id");
        $stmt->execute(['id' => $requestId]);

        //  Redirect back to the request list
        header("Location: travel_package_details.php");
        exit();
    } catch (PDOException $e) {
        die(" Database error: " . $e->getMessage());
    }
} else {
    //  Invalid access
    header("Location:  travel_package_details.php");
    exit();
}
