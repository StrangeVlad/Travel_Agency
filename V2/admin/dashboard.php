<?php
session_start();
if (!isset($_SESSION["admin"])) {
  header("Location: admin_login.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) die("Connection failed");

// Use correct column name `viewed_by_admin` and names `first_name`, `last_name`
$user_result = $conn->query("SELECT * FROM users WHERE viewed_by_admin = 0 ORDER BY created_at DESC");

$newUserCount = 0;
$notif_data = [];

while ($user = $user_result->fetch_assoc()) {
  $userId = $user['id'];
  $fullName = $user['first_name'] . ' ' . $user['last_name'];
  $type = null;
  $link = null;

  // Check Voyage Organisé booking
  $check_voyage = $conn->query("SELECT id FROM contact_requests WHERE email = '{$user['email']}' LIMIT 1");
  if ($check_voyage->num_rows > 0) {
    $type = "Voyage Organisé";
    $link = "voyage_org.php?user_id=$userId";
  }

  // Check Travel Package
  $check_package = $conn->query("SELECT id FROM travel_requests WHERE user_id = $userId LIMIT 1");
  if ($check_package->num_rows > 0 && !$type) {
    $type = "Package Personnalisé";
    $link = "travel_package_details.php?user_id=$userId";
  }

  // Check Classic Hotel Booking
  $check_mangr = $conn->query("SELECT id FROM reservations WHERE user_id = $userId LIMIT 1");
  if ($check_mangr->num_rows > 0 && !$type) {
    $type = "Réservation Hôtel";
    $link = "manage_bookings.php?user_id=$userId";
  }

  if ($type && $link) {
    $notif_data[] = [
      "name" => $fullName,
      "type" => $type,
      "link" => $link,
      "user_id" => $userId
    ];
    $newUserCount++;
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    /* Header */
    .header {
      background-color: #1976d2;
      color: white;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Menu */
    .menu,
    .notifications {
      position: relative;
    }

    .menu-btn {
      background-color: white;
      color: #1976d2;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: bold;
      cursor: pointer;
      border: none;
      transition: background-color 0.3s, color 0.3s;
    }

    .menu-btn:hover {
      background-color: #0d47a1;
      color: white;
    }

    .menu-content {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background-color: white;
      min-width: 200px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      border-radius: 10px;
      overflow: hidden;
    }

    .menu-content a {
      display: block;
      padding: 12px 16px;
      text-decoration: none;
      color: #333;
      transition: background-color 0.3s;
    }

    .menu-content a:hover {
      background-color: #eee;
    }

    .menu:hover .menu-content {
      display: block;
    }

    /* Search & Forms */
    .search-boxes {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 40px 0;
    }

    form {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    input[type="text"],
    select,
    button {
      padding: 12px 16px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      width: 250px;
    }

    button {
      background-color: #1976d2;
      color: white;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0d47a1;
    }

    /* Dashboard Links */
    .dashboard-links {
      max-width: 600px;
      margin: 30px auto;
      background-color: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .dashboard-links a {
      display: block;
      margin: 12px 0;
      padding: 15px;
      background-color: #1976d2;
      color: white;
      text-decoration: none;
      border-radius: 10px;
      font-size: 17px;
      text-align: center;
      transition: background-color 0.3s;
    }

    .dashboard-links a:hover {
      background-color: #0d47a1;
    }

    /* Notifications */
    .notif-icon {
      font-size: 24px;
      color: white;
      background: none;
      border: none;
      cursor: pointer;
      position: relative;
    }

    .notif-count {
      background-color: red;
      color: white;
      font-size: 12px;
      padding: 3px 6px;
      border-radius: 50%;
      position: absolute;
      top: -8px;
      right: -8px;
    }

    /* Notification Box */
    #notifBox {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      width: 300px;
      max-height: 300px;
      overflow-y: auto;
      background: white;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 999;
    }

    #notifBox ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    #notifBox li {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    #notifBox li a {
      text-decoration: none;
      color: #1976d2;
      font-weight: bold;
    }

    #notifBox li a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <div class="header">
    <h2>🛠 Admin Panel</h2>

    <!-- الإشعارات في الهيدر -->
    <div class="notifications">
      <button class="notif-icon" onclick="toggleNotif(); markNotificationsSeen();">
        🔔
        <?php if ($newUserCount > 0): ?>
          <span class="notif-count"><?= $newUserCount ?></span>
        <?php endif; ?>

      </button>
      <div id="notifBox">
        <ul>
          <?php foreach ($notif_data as $notif): ?>
            <li>
              <a href="<?= $notif['link'] ?>">
                <strong><?= htmlspecialchars($notif['name']) ?></strong><br>
                <?= htmlspecialchars($notif['type']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="menu">
      <button class="menu-btn">Menu</button>
      <div class="menu-content">
        <a href="manage_users.php">👥 Manage Users</a>
        <a href="manage_bookings.php">📑 Manage Bookings</a>
        <a href="voyage_org.php">✈️ Voyage Organize</a>
        <a href="travel_package_details.php">📦 Travel Packages</a>
        <a href="add_hotel.php">🏨 Manage Hotels</a>
        <a href="add_destination.php">🌍 Manage Destinations</a>
        <a href="admin_feedback.php">feedback</a>
        <a href="logout.php">🚪 Logout</a>
      </div>
    </div>
  </div>

  <h2 style="text-align: center;">🔍 Search Clients</h2>

  <div class="search-boxes">
    <form method="GET" action="search_client.php">
      <input type="hidden" name="search_type" value="client">
      <input type="text" name="query" placeholder="Search by name, email or birthday..." required>
      <button type="submit">Search</button>
    </form>

    <form method="GET" action="search_client.php">
      <input type="hidden" name="search_type" value="month">
      <select name="month" required>
        <option value="">-- Select Month --</option>
        <?php
        for ($m = 1; $m <= 12; $m++) {
          $monthValue = date('Y') . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
          $monthName = date('F', mktime(0, 0, 0, $m, 10));
          echo "<option value=\"$monthValue\">$monthName " . date('Y') . "</option>";
        }
        ?>
      </select>
      <button type="submit">Search by Month</button>
    </form>
  </div>

  <div class="dashboard-links">
    <a href="manage_users.php"> Manage Users</a>
    <a href="update.php">Edit_hotel and destination_ </a>
    <a href="logout.php"> Logout</a>
  </div>
  <script>
    function toggleNotif() {
      const box = document.getElementById("notifBox");
      box.style.display = (box.style.display === "block") ? "none" : "block";
    }

    function markNotificationsSeen() {
      fetch('mark_seen.php')
        .then(res => res.text())
        .then(console.log)
        .catch(err => console.error(err));
    }
  </script>

</body>

</html>