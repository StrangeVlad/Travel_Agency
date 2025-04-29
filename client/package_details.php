<?php
include 'includes/header.php';

// Check if package ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: packages.php");
  exit();
}

$package_id = intval($_GET['id']);

// Get package details
$sql = "SELECT * FROM packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: packages.php");
  exit();
}

$package = $result->fetch_assoc();
?>

<section class="package-details">
  <h1><?php echo $package['title']; ?></h1>

  <div class="package-images">
    <img src="../assets/img/placeholder.jpg" alt="<?php echo $package['destination']; ?>" class="main-image">
    <!-- Additional images could be added here -->
  </div>

  <div class="package-info">
    <div class="info-item">
      <span class="label">Destination:</span>
      <span class="value"><?php echo $package['destination']; ?></span>
    </div>
    <div class="info-item">
      <span class="label">Price:</span>
      <span class="value">$<?php echo $package['price']; ?> per person</span>
    </div>
    <div class="info-item">
      <span class="label">Dates:</span>
      <span class="value"><?php echo date("F d", strtotime($package['start_date'])); ?> -
        <?php echo date("F d, Y", strtotime($package['end_date'])); ?></span>
    </div>
    <div class="info-item">
      <span class="label">Duration:</span>
      <span class="value">
        <?php
        $start_date = new DateTime($package['start_date']);
        $end_date = new DateTime($package['end_date']);
        $duration = $start_date->diff($end_date)->days;
        echo $duration . ' days';
        ?>
      </span>
    </div>
    <div class="info-item">
      <span class="label">Available Slots:</span>
      <span class="value"><?php echo $package['available_slots']; ?> of <?php echo $package['total_slots']; ?></span>
    </div>
  </div>

  <div class="package-description">
    <h2>About This Package</h2>
    <p><?php echo nl2br($package['description']); ?></p>
  </div>

  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="booking-action">
      <?php if ($package['available_slots'] > 0): ?>
        <a href="booking.php?package_id=<?php echo $package['id']; ?>" class="book-now-button">Book Now</a>
      <?php else: ?>
        <p class="no-slots">Sorry, this package is fully booked.</p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="login-to-book">
      <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to book this package.</p>
    </div>
  <?php endif; ?>

  <div class="reviews-section">
    <h2>Customer Reviews</h2>

    <?php
    // Get approved reviews for this package
    $review_sql = "SELECT r.rating, r.comment, r.created_at, u.name FROM reviews r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE r.package_id = ? AND r.status = 'approved'
                      ORDER BY r.created_at DESC";
    $review_stmt = $conn->prepare($review_sql);
    $review_stmt->bind_param("i", $package_id);
    $review_stmt->execute();
    $reviews = $review_stmt->get_result();

    if ($reviews->num_rows > 0) {
      while ($review = $reviews->fetch_assoc()) {
        echo '<div class="review">';
        echo '<div class="review-header">';
        echo '<span class="reviewer-name">' . $review['name'] . '</span>';
        echo '<span class="review-date">' . date("M d, Y", strtotime($review['created_at'])) . '</span>';
        echo '<span class="rating">';
        for ($i = 1; $i <= 5; $i++) {
          if ($i <= $review['rating']) {
            echo '★';
          } else {
            echo '☆';
          }
        }
        echo '</span>';
        echo '</div>';
        echo '<p class="review-comment">' . nl2br($review['comment']) . '</p>';
        echo '</div>';
      }
    } else {
      echo '<p>No reviews yet for this package.</p>';
    }
    ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>