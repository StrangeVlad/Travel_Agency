<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 غير مصرح لك بالدخول إلى هذه الصفحة.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (isset($_POST['country_name'], $_POST['description'], $_POST['tour_type'])) {
    $country_name = $_POST['country_name'];
    $description = $_POST['description'];
    $tour_type = $_POST['tour_type'];

    // رفع الصورة
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // إنشاء مجلد إذا لم يكن موجودًا
        }

        $image = $uploadDir . time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // إدخال البيانات إلى قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO destinations (country_name, description, image, tour_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $country_name, $description, $image, $tour_type);

    if ($stmt->execute()) {
        echo "✅ تم إضافة الوجهة بنجاح! <br><a href='dashboard.php'>⬅ العودة إلى لوحة التحكم</a>";
    } else {
        echo "❌ خطأ: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "❌ البيانات غير مكتملة.";
}

$conn->close();
?>
