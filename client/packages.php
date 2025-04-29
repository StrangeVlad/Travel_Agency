<?php include 'includes/header.php'; ?>
<style>
  main {
    padding-top: 0;
  }
</style>

<section class="packages-section">
  <h1>Our Travel Packages</h1>

  <div class="filter-section">
    <form id="filter-form" method="get">
      <div class="form-group">
        <label for="destination">Destination</label>
        <input type="text" id="destination" name="destination" value="<?php echo isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="min-price">Min Price</label>
        <input type="number" id="min-price" name="min_price" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="max-price">Max Price</label>
        <input type="number" id="max-price" name="max_price" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
      </div>
      <button type="submit">Filter</button>
    </form>
  </div>

  <div class="package-grid">
    <?php
    // Build query based on filters
    $sql = "SELECT id, title, destination, description, price, start_date, end_date, available_slots 
                FROM packages 
                WHERE available_slots > 0";

    // Apply filters if set
    if (isset($_GET['destination']) && !empty($_GET['destination'])) {
      $destination = $conn->real_escape_string($_GET['destination']);
      $sql .= " AND destination LIKE '%$destination%'";
    }

    if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
      $min_price = floatval($_GET['min_price']);
      $sql .= " AND price >= $min_price";
    }

    if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
      $max_price = floatval($_GET['max_price']);
      $sql .= " AND price <= $max_price";
    }

    $sql .= " ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '<div class="package-card">';
        echo '<img src="../assets/img/placeholder.jpg" alt="' . $row["destination"] . '">';
        echo '<h3>' . $row["title"] . '</h3>';
        echo '<p class="destination">' . $row["destination"] . '</p>';
        echo '<p class="description">' . substr($row["description"], 0, 100) . '...</p>';
        echo '<p class="price">$' . $row["price"] . '</p>';
        echo '<p class="dates">' . date("M d", strtotime($row["start_date"])) . ' - ' .
          date("M d, Y", strtotime($row["end_date"])) . '</p>';
        echo '<p class="availability">Available slots: ' . $row["available_slots"] . '</p>';
        echo '<a href="package_details.php?id=' . $row["id"] . '" class="view-details">View Details</a>';
        echo '</div>';
      }
    } else {
      echo "<p>No packages found matching your criteria.</p>";
    }
    ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>