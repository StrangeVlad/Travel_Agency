<?php
session_start();

// Optional: check if the user is an admin before allowing deletion
if (!isset($_SESSION['admin'])) {
    die("Access denied. Admins only.");
}

// Check if ID is sent via POST
if (!isset($_POST['id'])) {
    die("❌ Invalid request. No ID provided.");
}

// Sanitize ID
$id = intval($_POST['id']);

// Database connection setup
$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete the request
    $stmt = $pdo->prepare("DELETE FROM contact_requests WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect back to the request list
    header("Location: all_requests.php");
    exit;

} catch (PDOException $e) {
    die("❌ Database error: " . $e->getMessage());
}
?>
