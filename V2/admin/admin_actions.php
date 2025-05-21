<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

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

$package_id = $_POST['package_id'] ?? null;
$action = $_POST['action'] ?? null;

if ($package_id && is_numeric($package_id) && in_array($action, ['validate', 'reject', 'delete'])) {
    switch ($action) {
        case 'validate':
            $stmt = $pdo->prepare("UPDATE travel_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$package_id]);
            header("Location: travel_package_details.php?status=validated");
            exit();
            
        case 'reject':
            $stmt = $pdo->prepare("UPDATE travel_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$package_id]);
            header("Location: travel_package_details.php?status=rejected");
            exit();

        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM travel_requests WHERE id = ?");
            $stmt->execute([$package_id]);
            header("Location: travel_package_details.php?status=deleted");
            exit();
    }
} else {
    header("Location: travel_package_details.php?status=error");
    exit();
}
?>
