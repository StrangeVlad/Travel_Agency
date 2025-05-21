<?php
session_start();

if (!isset($_SESSION['admin'])) {
    die("❌ Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if (!$request_id || !in_array($action, ['accept', 'refuse'])) {
        die("❌ Invalid form submission.");
    }

    // Connect to DB
    $pdo = new PDO("mysql:host=localhost;dbname=agence_voyage;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Save decision
    $stmt = $pdo->prepare("
        UPDATE contact_requests 
        SET admin_status = :status
        WHERE id = :id
    ");
    $stmt->execute([
        'status' => $action,
        'id' => $request_id
    ]);

    header("Location: voyage_org.php?status=success");
    exit;
}
?>
