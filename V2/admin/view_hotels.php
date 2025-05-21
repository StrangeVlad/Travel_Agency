<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("🚫 Unauthorized");
}

$pdo = new PDO("mysql:host=localhost;dbname=agence_voyage;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Fetch hotels with destination name
$stmt = $pdo->query("SELECT h.*, d.country_name FROM hotels h 
                     JOIN destinations d ON h.destination_id = d.destination_id 
                     ORDER BY h.hotel_id DESC");

echo "<h1>List of Hotels</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Price</th>
        <th>Rating</th>
        <th>Destination</th>
        <th>Images</th>
        <th>Services</th>
        <th>Description</th>
      </tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Handle multiple images (comma separated)
    $images = explode(",", $row['image']);
    $images_html = "";
    foreach ($images as $img) {
        $img = trim($img);
        if ($img) {
            $images_html .= "<img src='" . htmlspecialchars($img) . "' width='80' style='margin-right:5px;'>";
        }
    }

    // Fetch services for this hotel
    $serviceStmt = $pdo->prepare("SELECT service_name FROM hotel_services WHERE hotel_id = ?");
    $serviceStmt->execute([$row['hotel_id']]);
    $services = $serviceStmt->fetchAll(PDO::FETCH_COLUMN);
    $services_str = implode(", ", $services);

    // Handle description existence
    $description = isset($row['description']) ? htmlspecialchars($row['description']) : "N/A";

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['hotel_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
    echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
    echo "<td>" . htmlspecialchars($row['country_name']) . "</td>";
    echo "<td>$images_html</td>";
    echo "<td>" . htmlspecialchars($services_str) . "</td>";
    echo "<td>$description</td>";
    echo "</tr>";
}

echo "</table>";
?>
