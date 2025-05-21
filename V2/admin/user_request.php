<?php
session_start();

// 🔐 تحقق من الجلسة
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ معالجة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['request_id'], $_POST['action'])) {
        die("❌ البيانات ناقصة.");
    }

    $requestId = intval($_POST['request_id']);
    $action = $_POST['action'] === 'accept' ? 'accept' : 'refuse';

    // الاتصال بقاعدة البيانات
    $host = 'localhost';
    $db = 'agence_voyage';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ✅ أولاً: رفض الطلبات التي مضى عليها أكثر من يومين ولم يتم الدفع
        $pdo->exec("
            UPDATE travel_requests 
            SET admin_status = 'refuse' 
            WHERE admin_status IS NULL 
           TIMESTAMPDIFF(DAY, created_at, NOW()) >= 2
        ");

        // ✅ ثانياً: تنفيذ قرار الأدمن الحالي
        $stmt = $pdo->prepare("UPDATE travel_requests SET admin_status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $action,
            'id' => $requestId
        ]);

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();

    } catch (PDOException $e) {
        die("❌ خطأ في قاعدة البيانات: " . $e->getMessage());
    }
} else {
    die("❌ طريقة غير صحيحة للوصول.");
}
