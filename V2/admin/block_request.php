<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Read POST data safely
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : null;
$action = $_POST['action'] ?? null;

// Validate
if ($user_id && $package_id && in_array($action, ['block', 'unblock'])) {
    $is_blocked = $action === 'block' ? 1 : 0;

    // Update the travel_requests table
    $stmt = $pdo->prepare("UPDATE travel_requests SET is_blocked = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$is_blocked, $package_id, $user_id]);

    header("Location: travel_package_details.php?id=" . urlencode($package_id));
    exit();
} else {
    echo "❌ Invalid request: Missing or invalid parameters.";
}
?>
