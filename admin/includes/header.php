<?php
include '../res/db_connection.php';
include 'includes/functions.php';
check_admin_login();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travel Agency Admin</title>
  <link rel="stylesheet" href="../assets/css/includes.css">
  <link rel="stylesheet" href="../assets/css/admin-style.css">

</head>

<body>
  <header>
    <h1>Travel Agency Admin Panel</h1>
    <div class="user-info">
      <div class="user-avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
      <div class="user-details">
        <p class="user-name"><?php echo $_SESSION['name']; ?></p>
        <p class="user-role"><?php echo ucfirst($_SESSION['role']); ?></p>
      </div>
      <a href="logout.php" class="logout-btn">
        <span>Logout</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
      </a>
    </div>
    <button class="mobile-menu-toggle" aria-label="Toggle menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
    </button>
    <nav>
      <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="packages.php">Packages</a></li>
        <li><a href="hotels.php">Hotels</a></li>
        <li><a href="hotel_rooms.php">Hotel Rooms</a></li>
        <li><a href="meals.php">Meals</a></li>
        <li><a href="bookings.php">Bookings</a></li>
        <li><a href="reviews.php">Reviews</a></li>
        <li><a href="tickets.php">Support Tickets</a></li>
      </ul>
    </nav>
  </header>
  <main>