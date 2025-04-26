<?php
include 'includes/db_connection.php';
include 'includes/functions.php';
check_admin_login();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travel Agency Admin</title>
</head>

<body>
  <header>
    <h1>Travel Agency Admin Panel</h1>
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
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>
  <main>