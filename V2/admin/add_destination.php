<?php
$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM destinations ORDER BY destination_id DESC");

echo "<h1>🌍 الوجهات المتوفرة</h1>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border:1px solid #ccc; padding:15px; margin:10px;'>";
        echo "<h2>" . htmlspecialchars($row['country_name']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
        if (!empty($row['image'])) {
            echo "<img src='" . $row['image'] . "' width='300'><br>";
        }
        echo "<strong>نوع الجولة:</strong> " . $row['tour_type'];
        echo "</div>";
    }
} else {
    echo "❗ لا توجد وجهات حالياً.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Destination</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #00796b;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin: 10px 0 5px;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        input[type="submit"] {
            background-color: #00796b;
            color: white;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #004d40;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #00796b;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add a New Destination</h1>
        <form action="save_destination.php" method="POST" enctype="multipart/form-data">
            <label for="country_name">Country Name:</label>
            <input type="text" name="country_name" required><br><br>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea><br><br>

            <label for="image">Image:</label>
            <input type="file" name="image" accept="image/*"><br><br>

            <label for="tour_type">Tour Type:</label>
            <select name="tour_type">
                <option value="National">National Tours</option>
                <option value="International">International Trips</option>
            </select><br><br>

            <input type="submit" value="Add Destination">
        </form>
        <div class="back-link">
            <a href="dashboard.php"> ⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
