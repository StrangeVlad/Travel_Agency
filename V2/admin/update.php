<?php
$conn = new mysqli("localhost", "root", "", "agence_voyage");
$conn->set_charset("utf8mb4");

// حذف الوجهة
if (isset($_GET['delete_destination'])) {
    $id = intval($_GET['delete_destination']);
    $conn->query("DELETE FROM destinations WHERE destination_id = $id");
    echo "<div class='message delete'>🗑️ Destination deleted.</div>";
}

// حذف الفندق
if (isset($_GET['delete_hotel'])) {
    $id = intval($_GET['delete_hotel']);
    $conn->query("DELETE FROM hotels WHERE hotel_id = $id");
    echo "<div class='message delete'>🗑️ Hotel deleted.</div>";
}

// تعديل الوجهة
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_destination'])) {
    $id = $_POST["destination_id"];
    $country_name = $_POST["country_name"];
    $description = $_POST["description"];
    $tour_type = $_POST["tour_type"];

    $stmt = $conn->prepare("UPDATE destinations SET country_name=?, description=?, tour_type=? WHERE destination_id=?");
    $stmt->bind_param("sssi", $country_name, $description, $tour_type, $id);
    $stmt->execute();
    echo "<div class='message success'>✔ Destination updated.</div>";
}

// تعديل الفندق
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_hotel'])) {
    $id = $_POST["hotel_id"];
    $name = $_POST["name"];
    $location = $_POST["location"];
    $price = $_POST["price"];
    $rating = $_POST["rating"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, price=?, rating=?, description=? WHERE hotel_id=?");
    $stmt->bind_param("ssdisi", $name, $location, $price, $rating, $description, $id);
    $stmt->execute();
    echo "<div class='message success'>✔ Hotel updated.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Edit Hotels and Destinations</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 30px;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 1 1 350px;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        form textarea {
            resize: vertical;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        .actions button,
        .actions a {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        .actions button {
            background-color: #007bff;
            color: white;
        }

        .actions a {
            background-color: #dc3545;
            color: white;
        }

        .message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.delete {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h2>🗺️ Manage Destinations</h2>
<a href="dashboard.php">⬅ Back to Dashboard</a><br><br>
<div class="container">
<?php
$result = $conn->query("SELECT * FROM destinations");
while ($dest = $result->fetch_assoc()) {
?>
    <div class="card">
        <form method="POST">
            <input type="hidden" name="destination_id" value="<?= $dest['destination_id'] ?>">
            <label>Country Name:</label>
            <input type="text" name="country_name" value="<?= htmlspecialchars($dest['country_name']) ?>" required>
            <label>Tour Type:</label>
            <input type="text" name="tour_type" value="<?= htmlspecialchars($dest['tour_type']) ?>">
            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($dest['description']) ?></textarea>
            <div class="actions">
                <button type="submit" name="update_destination">💾 Update</button>
                <a href="?delete_destination=<?= $dest['destination_id'] ?>" onclick="return confirm('Delete this destination?')">🗑️ Delete</a>
            </div>
        </form>
    </div>
<?php } ?>
</div>

<h2>🏨 Manage Hotels by Country</h2>
<div class="container">
<?php
$result = $conn->query("
    SELECT hotels.*, destinations.country_name 
    FROM hotels 
    JOIN destinations ON hotels.destination_id = destinations.destination_id 
    ORDER BY destinations.country_name ASC
");

$current_country = "";
while ($hotel = $result->fetch_assoc()) {
    if ($current_country != $hotel['country_name']) {
        // بداية قسم جديد لدولة
        if ($current_country != "") {
            echo "</div>"; // إغلاق الدولة السابقة
        }
        $current_country = $hotel['country_name'];
        echo "<h3>🌍 Hotels in <span style='color:#007bff'>" . htmlspecialchars($current_country) . "</span></h3>";
        echo "<div class='container'>";
    }
?>
    <div class="card">
        <form method="POST">
            <input type="hidden" name="hotel_id" value="<?= $hotel['hotel_id'] ?>">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($hotel['name']) ?>" required>
            <label>Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($hotel['location']) ?>">
            <label>Price ($):</label>
            <input type="number" step="0.01" name="price" value="<?= $hotel['price'] ?>">
            <label>Rating:</label>
            <input type="number" step="0.1" name="rating" value="<?= $hotel['rating'] ?>">
            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($hotel['description']) ?></textarea>
            <div class="actions">
                <button type="submit" name="update_hotel">💾 Update</button>
                <a href="?delete_hotel=<?= $hotel['hotel_id'] ?>" onclick="return confirm('Delete this hotel?')">🗑️ Delete</a>
            </div>
        </form>
    </div>
<?php
}
if ($current_country != "") {
    echo "</div>"; // إغلاق آخر مجموعة دولة
}
?>


</body>
</html>
