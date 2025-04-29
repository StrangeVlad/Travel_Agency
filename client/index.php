<?php include 'includes/header.php'; ?>
<style>
  /* ===== Fix Why-Choose-Us & Feature Styles ===== */

  /* Section wrapper */
  .why-choose-us {
    padding: 4rem 2rem;
    background: #f7f9fc;
  }

  .why-choose-us h2 {
    font-family: 'Montserrat', sans-serif;
    font-size: 2.75rem;
    color: var(--primary);
    text-align: center;
    margin-bottom: 2.5rem;
    letter-spacing: 1px;
  }

  /* Features grid */
  .features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
  }

  /* Single feature card */
  .feature {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    width: 240px;
    text-align: center;
    box-shadow: 0 4px 16px var(--shadow-light);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
  }

  .feature:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 32px var(--shadow-strong);
  }

  /* Icon circle */
  .feature::before {
    content: '';
    display: inline-block;
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 2rem;
    width: 48px;
    height: 48px;
    line-height: 48px;
    border-radius: 50%;
    background: var(--primary-light);
    color: #fff;
    margin-bottom: 1rem;
  }

  /* Specific icons */
  .feature:nth-child(1)::before {
    content: "\f0d6";
    /* money-bill */
    background: #ff6b6b;
  }

  .feature:nth-child(2)::before {
    content: "\f508";
    /* user-tie */
    background: #1a73e8;
  }

  .feature:nth-child(3)::before {
    content: "\f594";
    /* hotel */
    background: #ffb300;
  }

  .feature:nth-child(4)::before {
    content: "\f59e";
    /* headset */
    background: #13c2c2;
  }

  /* Feature title & text */
  .feature h3 {
    font-size: 1.25rem;
    color: var(--text-heading);
    margin: 0.75rem 0;
  }

  .feature p {
    font-size: 0.9rem;
    color: var(--text-body);
    line-height: 1.4;
  }
</style>
<section class="hero">
  <h1>Discover Amazing Destinations</h1>
  <p>Find your perfect travel package and explore the world with us.</p>
  <a href="packages.php" class="cta-button">Browse Packages</a>
</section>

<section class="featured-packages">
  <h2>Featured Travel Packages</h2>
  <div class="package-grid">
    <?php
    // Get featured packages (limited to 4)
    $sql = "SELECT id, title, destination, price, start_date, end_date, available_slots, image 
            FROM packages 
            WHERE available_slots > 0 
            ORDER BY created_at DESC LIMIT 4";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '<div class="package-card">';

        // Check if package has an image
        if (!empty($row["image"])) {
          echo '<img src="../assets/uploads/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["destination"]) . '">';
        } else {
          echo '<img src="../assets/img/placeholder.jpg" alt="' . htmlspecialchars($row["destination"]) . '">';
        }

        echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
        echo '<p class="destination">' . htmlspecialchars($row["destination"]) . '</p>';
        echo '<p class="price">$' . htmlspecialchars($row["price"]) . '</p>';
        echo '<p class="dates">' . date("M d", strtotime($row["start_date"])) . ' - ' . date("M d, Y", strtotime($row["end_date"])) . '</p>';
        echo '<p class="availability">Available slots: ' . htmlspecialchars($row["available_slots"]) . '</p>';
        echo '<a href="package_details.php?id=' . $row["id"] . '" class="view-details">View Details</a>';
        echo '</div>';
      }
    } else {
      echo "<p>No packages available at the moment.</p>";
    }
    ?>
  </div>
  <a href="packages.php" class="view-all">View All Packages</a>
</section>

<section class="why-choose-us">
  <h2>Why Choose Our Travel Agency?</h2>
  <div class="features">
    <div class="feature">
      <h3>Best Prices</h3>
      <p>We offer competitive prices and great value for your money.</p>
    </div>
    <div class="feature">
      <h3>Experienced Guides</h3>
      <p>Our professional guides will make your journey unforgettable.</p>
    </div>
    <div class="feature">
      <h3>Quality Accommodations</h3>
      <p>Stay at carefully selected hotels with great amenities.</p>
    </div>
    <div class="feature">
      <h3>24/7 Support</h3>
      <p>We're always here to help, before, during, and after your trip.</p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>