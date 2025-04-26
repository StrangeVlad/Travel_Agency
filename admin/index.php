<?php include 'includes/header.php'; ?>

<h2>Dashboard</h2>

<div class="dashboard-stats">
  <?php
  // Get package count
  $sql = "SELECT COUNT(*) as count FROM packages";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $package_count = $row['count'];

  // Get hotel count
  $sql = "SELECT COUNT(*) as count FROM hotels";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $hotel_count = $row['count'];

  // Get booking count
  $sql = "SELECT COUNT(*) as count FROM bookings";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $booking_count = $row['count'];

  // Get user count
  $sql = "SELECT COUNT(*) as count FROM users";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $user_count = $row['count'];

  // Get pending reviews count
  $sql = "SELECT COUNT(*) as count FROM reviews WHERE status = 'pending'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $pending_review_count = $row['count'];

  // Get open tickets count
  $sql = "SELECT COUNT(*) as count FROM tickets WHERE status = 'open'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $open_ticket_count = $row['count'];
  ?>

  <div class="stat-box">
    <h3>Packages</h3>
    <p><?php echo $package_count; ?></p>
    <a href="packages.php">View All</a>
  </div>

  <div class="stat-box">
    <h3>Hotels</h3>
    <p><?php echo $hotel_count; ?></p>
    <a href="hotels.php">View All</a>
  </div>

  <div class="stat-box">
    <h3>Bookings</h3>
    <p><?php echo $booking_count; ?></p>
    <a href="bookings.php">View All</a>
  </div>

  <div class="stat-box">
    <h3>Users</h3>
    <p><?php echo $user_count; ?></p>
  </div>

  <div class="stat-box">
    <h3>Pending Reviews</h3>
    <p><?php echo $pending_review_count; ?></p>
    <a href="reviews.php">View All</a>
  </div>

  <div class="stat-box">
    <h3>Open Tickets</h3>
    <p><?php echo $open_ticket_count; ?></p>
    <a href="tickets.php">View All</a>
  </div>
</div>

<div class="recent-bookings">
  <h3>Recent Bookings</h3>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Package</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT b.id, u.name as user_name, p.title as package_title, 
                    b.travel_date, b.status 
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN packages p ON b.package_id = p.id
                    ORDER BY b.created_at DESC LIMIT 5";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['user_name'] . "</td>";
          echo "<td>" . $row['package_title'] . "</td>";
          echo "<td>" . $row['travel_date'] . "</td>";
          echo "<td>" . $row['status'] . "</td>";
          echo "<td><a href='bookings.php?view=" . $row['id'] . "'>View</a></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No recent bookings</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>