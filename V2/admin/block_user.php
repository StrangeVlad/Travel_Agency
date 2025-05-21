<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    // Create PDO instance and set error mode
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

$user_id = $_POST['user_id'] ?? null;
$action = $_POST['action'] ?? null;

if ($user_id && $action) {
    switch ($action) {
        case 'block':
            // حظر المستخدم
            $stmt = $pdo->prepare("UPDATE users SET is_blocked = 1 WHERE id = ?");
            $stmt->execute([$user_id]);
            header("Location: travel_package_details.php?id=" . $_POST['package_id']);
            break;

        case 'unblock':
            // إلغاء حظر المستخدم
            $stmt = $pdo->prepare("UPDATE users SET is_blocked = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
            header("Location: travel_package_details.php?id=" . $_POST['package_id']);
            break;

        default:
            header("Location: travel_package_details.php?id=" . $_POST['package_id']);
            break;
    }
} else {
    header("Location: dashboard.php");
}
exit();
?>
