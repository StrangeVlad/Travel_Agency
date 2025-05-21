<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    die("⛔ Unauthorized access. Please log in.");
}

if (!isset($_GET["hotel"])) {
    die("❌ Hotel ID not provided.");
}

$hotel_id = intval($_GET["hotel"]);

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$result = $conn->query("
    SELECT h.name AS hotel_name, h.location, h.price, h.rating, d.name AS destination_name
    FROM hotels h
    JOIN destinations d ON h.destination_id = d.id
    WHERE h.id = $hotel_id
");

if ($result->num_rows === 0) {
    die("🚫 Hotel not found.");
}

$hotel = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            text-align: center;
            padding: 50px;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        h1 {
            color: #2e7d32;
        }
        .details {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
        .details strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>✅ Thank You for Your Booking!</h1>
        <p>Your reservation was successfully submitted.</p>

        <div class="details">
            <p><strong>Hotel:</strong> <?= htmlspecialchars($hotel['name']) ?></p>
            <p><strong>Destination:</strong> <?= htmlspecialchars($hotel['destination_name']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($hotel['location']) ?></p>
            <p><strong>Price per night:</strong> <?= htmlspecialchars($hotel['price']) ?> DA</p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($hotel['rating']) ?> ⭐</p>
        </div>
    </div>
</body>
</html>

