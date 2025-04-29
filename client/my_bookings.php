<?php
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get user's bookings
$sql = "SELECT b.*, p.title, p.destination, p.price 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="my-bookings-section">
  <h1>My Bookings</h1>

  <?php if ($result->num_rows === 0): ?>
    <div class="no-bookings">
      <p>You haven't made any bookings yet.</p>
      <a href="packages.php" class="browse-packages">Browse Travel Packages</a>
    </div>
  <?php else: ?>
    <div class="bookings-list">
      <?php while ($booking = $result->fetch_assoc()): ?>
        <div class="booking-card">
          <div class="booking-header">
            <h3><?php echo $booking['title']; ?></h3>
            <span class="booking-status <?php echo strtolower($booking['status']); ?>">
              <?php echo ucfirst($booking['status']); ?>
            </span>
          </div>

          <div class="booking-details">
            <div class="detail-item">
              <span class="label">Booking ID:</span>
              <span class="value">#<?php echo $booking['id']; ?></span>
            </div>
            <div class="detail-item">
              <span class="label">Destination:</span>
              <span class="value"><?php echo $booking['destination']; ?></span>
            </div>
            <div class="detail-item">
              <span class="label">Travel Date:</span>
              <span class="value"><?php echo date("F d, Y", strtotime($booking['travel_date'])); ?></span>
            </div>
            <div class="detail-item">
              <span class="label">People:</span>
              <span class="value"><?php echo $booking['num_people']; ?></span>
            </div>
            <div class="detail-item">
              <span class="label">Base Price:</span>
              <span class="value">$<?php echo $booking['price']; ?> per person</span>
            </div>
            <div class="detail-item">
              <span class="label">Booked On:</span>
              <span class="value"><?php echo date("M d, Y", strtotime($booking['created_at'])); ?></span>
            </div>
          </div>

          <div class="booking-actions">
            <a href="booking_details.php?id=<?php echo $booking['id']; ?>" class="view-details">View Details</a>

            <?php if ($booking['status'] === 'booked'): ?>
              <button class="cancel-booking" data-booking-id="<?php echo $booking['id']; ?>">Cancel Booking</button>
            <?php endif; ?>

            <?php if ($booking['status'] === 'completed'): ?>
              <a href="write_review.php?package_id=<?php echo $booking['package_id']; ?>" class="write-review">Write Review</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</section>

<script>
  // Add event listeners to cancel booking buttons
  document.querySelectorAll('.cancel-booking').forEach(function(button) {
    button.addEventListener('click', function() {
      if (confirm('Are you sure you want to cancel this booking?')) {
        const bookingId = this.getAttribute('data-booking-id');

        // AJAX request to cancel booking
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'actions/cancel_booking.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
          if (this.status === 200) {
            const response = JSON.parse(this.responseText);

            if (response.success) {
              // Reload page to update booking status
              location.reload();
            } else {
              alert(response.message);
            }
          } else {
            alert('An error occurred. Please try again.');
          }
        };

        xhr.send('booking_id=' + encodeURIComponent(bookingId));
      }
    });
  });
</script>

<?php include 'includes/footer.php'; ?>