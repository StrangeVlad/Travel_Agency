<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 You are not authorized to access this page.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// هنا عملية اضافة الفندق لو تم ارسال الفورم
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $description = $conn->real_escape_string($_POST['description']);
    $destination_id = intval($_POST['destination_id']);
    $other_services = $conn->real_escape_string($_POST['other_services']);

    $services = isset($_POST['services']) ? $_POST['services'] : [];
    if (!empty($other_services)) {
        $services[] = $other_services;
    }
    $services_str = implode(", ", $services);

    // رفع الصور
    $image_paths = [];
  if (!empty($_FILES['image']['name'][0])) {
    $upload_dir = "uploads/hotels/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        $filename = basename($_FILES['image']['name'][$key]);
        $target_file = $upload_dir . uniqid() . "_" . $filename;

        if (move_uploaded_file($tmp_name, $target_file)) {
            // خزن مسار الصورة في جدول hotel_images لكل صورة على حدى
            $image_stmt = $conn->prepare("INSERT INTO hotel_images (hotel_id, image_url) VALUES (?, ?)");
            $image_stmt->bind_param("is", $hotel_id, $target_file);
            $image_stmt->execute();
            $image_stmt->close();
        }
    }
}

    $images_str = implode(",", $image_paths);

    $stmt = $conn->prepare("INSERT INTO hotels (name, location, price, rating,description, destination_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdids", $name, $location, $price, $rating,$description, $destination_id, $images);

    if ($stmt->execute()) {
        $hotel_id = $conn->insert_id;

        foreach ($services as $service) {
            $service_stmt = $conn->prepare("INSERT INTO hotel_services (hotel_id, service_name) VALUES (?, ?)");
            $service_stmt->bind_param("is", $hotel_id, $service);
            $service_stmt->execute();
            $service_stmt->close();
        }

        echo "<script>alert('✅ Hotel added successfully!'); window.location.href = 'view_hotels.php';</script>";
        exit;
    } else {
        echo "<p style='color:red;'>❌ Error adding hotel: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Hotel</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef5f9;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #00796b;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        form label {
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        form textarea {
            height: 120px;
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .services-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .service-checkbox {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .service-checkbox input {
            transform: scale(1.2);
        }

        button {
            background-color: #00796b;
            color: white;
            border: none;
            padding: 15px;
            font-size: 17px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s ease;
            grid-column: 1 / -1;
        }

        button:hover {
            background-color: #004d40;
        }

        .back-link {
            margin-top: 30px;
            text-align: center;
        }

        .back-link a {
            color: #00796b;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛎️ Add a New Hotel</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div>
                <label for="name">Hotel Name:</label>
                <input type="text" name="name" required>
            </div>

            <div>
                <label for="location">Location:</label>
                <input type="text" name="location" required>
            </div>

            <div>
                <label for="price">Price (USD):</label>
                <input type="number" name="price" step="0.01" required>
            </div>

            <div>
                <label for="rating">Rating (0-5):</label>
                <input type="number" name="rating" step="0.1" min="0" max="5" required>
            </div>

            <div class="full-width">
                <label for="image">Hotel Images:</label>
                <input type="file" name="image[]" accept="image/*" multiple>
            </div>

     <div class="full-width">
    <label for="destination_id">Hotel Destination:</label>
    <select name="destination_id" required>
        <option value="">-- Select Destination --</option>
        <?php
        $result = $conn->query("SELECT destination_id, country_name FROM destinations ORDER BY country_name ASC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['destination_id']) . '">' . htmlspecialchars($row['country_name']) . '</option>';
            }
            $result->free();
        }
        ?>
    </select>
</div>

            <div class="full-width">
                <label for="services">Hotel Services:</label>
                <div class="services-container">
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Wi-Fi"> Wi-Fi
                    </div>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Pool"> Pool
                    </div>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Spa"> Spa
                    </div>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Gym"> Gym
                    </div>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Restaurant"> Restaurant
                    </div>
                    <div class="service-checkbox">
                        <input type="checkbox" name="services[]" value="Bar"> Bar
                    </div>
                </div>
            </div>

            <div class="full-width">
                <label for="other_services">Other Service(s):</label>
                <input type="text" name="other_services" placeholder="e.g. Babysitting, Shuttle, Laundry">
            </div>
<div class="full-width">
    <label for="description">Hotel Description:</label>
    <textarea name="description" placeholder="Write a detailed hotel description..." required></textarea>
</div>
            <button type="submit">➕ Add Hotel</button>
        </form>

        <div class="back-link">
            <a href="dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
