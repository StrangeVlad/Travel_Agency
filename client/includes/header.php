<?php
session_start();
require_once '../res/db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travel Agency</title>
  <link rel="stylesheet" href="../assets/css/client-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/includes-client.css">
</head>

<body>
  <header>
    <nav>
      <div class="logo">Travel Agency</div>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="packages.php">Travel Packages</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">My Profile</a></li>
          <li><a href="my_bookings.php">My Bookings</a></li>
          <li><a href="support.php">Support</a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>