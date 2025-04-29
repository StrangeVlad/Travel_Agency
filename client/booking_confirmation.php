<?php
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if booking ID is set
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
  header("Location: my_bookings.php");
  exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

// Get booking details
$sql = "SELECT b.*, p.title, p.destination, p.price 
        FROM bookings b 
        JOIN packages p ON b.package_id = p.id 
        WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: my_bookings.php");
  exit();
}

$booking = $result->fetch_assoc();

// Get payment details
$payment_sql = "SELECT * FROM payments WHERE booking_id = ? LIMIT 1";
$payment_stmt = $conn->prepare($payment_sql);
$payment_stmt->bind_param("i", $booking_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$payment = $payment_result->fetch_assoc();
?>

<section class="booking-confirmation">
  <div class="confirmation-header">
    <h1>Booking Confirmed!</h1>
    <p class="confirmation-message">Your booking for <?php echo $booking['title']; ?> has been successfully placed.</p>
  </div>

  <div class="booking-details">
    <h2>Booking Details</h2>

    <div class="confirmation-box">
      <div class="detail-row">
        <span class="label">Booking Reference:</span>
        <span class="value">#<?php echo $booking['id']; ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Package:</span>
        <span class="value"><?php echo $booking['title']; ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Destination:</span>
        <span class="value"><?php echo $booking['destination']; ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Travel Date:</span>
        <span class="value"><?php echo date("F d, Y", strtotime($booking['travel_date'])); ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Number of People:</span>
        <span class="value"><?php echo $booking['num_people']; ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Status:</span>
        <span class="value"><?php echo ucfirst($booking['status']); ?></span>
      </div>
    </div>
  </div>

  <?php if (isset($payment)): ?>
    <div class="payment-details">
      <h2>Payment Information</h2>

      <div class="confirmation-box">
        <div class="detail-row">
          <span class="label">Amount Paid:</span>
          <span class="value">$<?php echo $payment['amount']; ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Payment Method:</span>
          <span class="value"><?php echo str_replace('_', ' ', ucfirst($payment['payment_method'])); ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Transaction ID:</span>
          <span class="value"><?php echo $payment['transaction_id']; ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Payment Date:</span>
          <span class="value"><?php echo date("F d, Y", strtotime($payment['payment_date'])); ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Payment Status:</span>
          <span class="value"><?php echo ucfirst($payment['status']); ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="next-steps">
    <h2>What's Next?</h2>
    <ul>
      <li>Check your email for a confirmation of your booking.</li>
      <li>Review your booking details in the <a href="my_bookings.php">My Bookings</a> section.</li>
      <li>If you have any questions, please contact our <a href="support.php">Support Team</a>.</li>
    </ul>
  </div>

  <div class="confirmation-actions">
    <a href="my_bookings.php" class="view-bookings">View All My Bookings</a>
    <a href="packages.php" class="browse-more">Browse More Packages</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>