<?php
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if package ID is set
if (!isset($_GET['package_id']) || empty($_GET['package_id'])) {
  header("Location: packages.php");
  exit();
}

$package_id = intval($_GET['package_id']);

// Get package details
$sql = "SELECT * FROM packages WHERE id = ? AND available_slots > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: packages.php");
  exit();
}

$package = $result->fetch_assoc();

// Get available hotels
$hotel_sql = "SELECT h.id, h.name, h.rating, h.destination FROM hotels h WHERE h.destination = ?";
$hotel_stmt = $conn->prepare($hotel_sql);
$hotel_stmt->bind_param("s", $package['destination']);
$hotel_stmt->execute();
$hotels_result = $hotel_stmt->get_result();

// Get available meals
$meal_sql = "SELECT * FROM meals";
$meals_result = $conn->query($meal_sql);
?>

<section class="booking-section">
  <h1>Book Your Trip to <?php echo $package['destination']; ?></h1>

  <div class="package-summary">
    <h2><?php echo $package['title']; ?></h2>
    <p class="dates"><?php echo date("F d", strtotime($package['start_date'])); ?> -
      <?php echo date("F d, Y", strtotime($package['end_date'])); ?></p>
    <p class="price">$<?php echo $package['price']; ?> per person</p>
  </div>

  <div id="error-message" class="error-message"></div>

  <form id="booking-form" method="post" action="actions/book_package.php">
    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">

    <div class="form-group">
      <label for="travel_date">Travel Date</label>
      <input type="date" id="travel_date" name="travel_date"
        min="<?php echo $package['start_date']; ?>"
        max="<?php echo $package['end_date']; ?>" required>
    </div>

    <div class="form-group">
      <label for="num_people">Number of People</label>
      <input type="number" id="num_people" name="num_people" min="1"
        max="<?php echo $package['available_slots']; ?>" value="1" required>
    </div>

    <div class="form-group">
      <label for="hotel_id">Select Hotel</label>
      <select id="hotel_id" name="hotel_id" required>
        <option value="">-- Select a Hotel --</option>
        <?php
        if ($hotels_result->num_rows > 0) {
          while ($hotel = $hotels_result->fetch_assoc()) {
            echo '<option value="' . $hotel['id'] . '">' . $hotel['name'] . ' (' . $hotel['rating'] . ' ★)</option>';
          }
        }
        ?>
      </select>
    </div>

    <div class="form-group hotel-rooms" style="display: none;">
      <label for="hotel_room_id">Select Room Type</label>
      <select id="hotel_room_id" name="hotel_room_id" required disabled>
        <option value="">-- Select a Room Type --</option>
      </select>
    </div>

    <div class="form-group">
      <label for="nights">Number of Nights</label>
      <input type="number" id="nights" name="nights" min="1" value="1" required>
    </div>

    <div class="form-group">
      <label>Select Meals (Optional)</label>
      <div class="meal-options">
        <?php
        if ($meals_result->num_rows > 0) {
          while ($meal = $meals_result->fetch_assoc()) {
            echo '<div class="meal-option">';
            echo '<input type="checkbox" id="meal_' . $meal['id'] . '" name="meals[]" value="' . $meal['id'] . '">';
            echo '<label for="meal_' . $meal['id'] . '">' . $meal['name'] . ' ($' . $meal['price'] . ')</label>';
            echo '</div>';
          }
        }
        ?>
      </div>
    </div>

    <div class="form-group">
      <label for="special_requests">Special Requests (Optional)</label>
      <textarea id="special_requests" name="special_requests" rows="4"></textarea>
    </div>

    <div class="total-price-section">
      <h3>Estimated Total: $<span id="total-price">0.00</span></h3>
      <p class="price-note">Final price will be calculated upon confirmation.</p>
    </div>

    <button type="submit" id="book-button">Book Now</button>
  </form>
</section>

<script>
  // Function to fetch hotel rooms based on selected hotel
  document.getElementById('hotel_id').addEventListener('change', function() {
    const hotelId = this.value;
    const hotelRoomSelect = document.getElementById('hotel_room_id');
    const hotelRoomsDiv = document.querySelector('.hotel-rooms');

    if (hotelId) {
      // AJAX request to get hotel rooms
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'actions/fetch_hotel_rooms.php?hotel_id=' + hotelId, true);

      xhr.onload = function() {
        if (this.status === 200) {
          const rooms = JSON.parse(this.responseText);

          // Clear previous options
          hotelRoomSelect.innerHTML = '<option value="">-- Select a Room Type --</option>';

          // Add new options
          rooms.forEach(function(room) {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = room.room_type + ' ($' + room.price_per_night + ' per night)';
            option.dataset.price = room.price_per_night;
            hotelRoomSelect.appendChild(option);
          });

          hotelRoomSelect.disabled = false;
          hotelRoomsDiv.style.display = 'block';

          // Update total price
          updateTotalPrice();
        }
      };

      xhr.send();
    } else {
      hotelRoomSelect.innerHTML = '<option value="">-- Select a Room Type --</option>';
      hotelRoomSelect.disabled = true;
      hotelRoomsDiv.style.display = 'none';

      // Update total price
      updateTotalPrice();
    }
  });

  // Function to update total price
  function updateTotalPrice() {
    const packagePrice = <?php echo $package['price']; ?>;
    const numPeople = document.getElementById('num_people').value;
    const nights = document.getElementById('nights').value;
    const hotelRoomSelect = document.getElementById('hotel_room_id');
    const mealCheckboxes = document.querySelectorAll('input[name="meals[]"]:checked');
    const totalPriceElement = document.getElementById('total-price');

    let totalPrice = packagePrice * numPeople;

    // Add hotel room price if selected
    if (hotelRoomSelect.selectedIndex > 0) {
      const selectedRoom = hotelRoomSelect.options[hotelRoomSelect.selectedIndex];
      const roomPrice = parseFloat(selectedRoom.dataset.price);
      totalPrice += roomPrice * nights;
    }

    // Add meal prices
    mealCheckboxes.forEach(function(checkbox) {
      const mealId = checkbox.value;
      <?php
      // Create a JavaScript object with meal prices
      echo "const mealPrices = {";
      if ($meals_result->num_rows > 0) {
        $meals_result->data_seek(0);
        $first = true;
        while ($meal = $meals_result->fetch_assoc()) {
          if (!$first) echo ", ";
          echo $meal['id'] . ": " . $meal['price'];
          $first = false;
        }
      }
      echo "};";
      ?>

      totalPrice += mealPrices[mealId] * numPeople;
    });

    totalPriceElement.textContent = totalPrice.toFixed(2);
  }

  // Add event listeners for price calculations
  document.getElementById('num_people').addEventListener('change', updateTotalPrice);
  document.getElementById('nights').addEventListener('change', updateTotalPrice);
  document.getElementById('hotel_room_id').addEventListener('change', updateTotalPrice);

  // Add event listeners for all meal checkboxes
  document.querySelectorAll('input[name="meals[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', updateTotalPrice);
  });

  // Form submission
  document.getElementById('booking-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const bookButton = document.getElementById('book-button');
    const errorMessage = document.getElementById('error-message');

    // Disable button to prevent multiple submissions
    bookButton.disabled = true;
    bookButton.textContent = 'Processing...';

    // AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'actions/book_package.php', true);

    xhr.onload = function() {
      if (this.status === 200) {
        const response = JSON.parse(this.responseText);

        if (response.success) {
          // Redirect to booking confirmation page
          window.location.href = 'booking_confirmation.php?booking_id=' + response.booking_id;
        } else {
          // Display error message
          errorMessage.textContent = response.message;
          errorMessage.style.display = 'block';
          bookButton.disabled = false;
          bookButton.textContent = 'Book Now';
        }
      } else {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        bookButton.disabled = false;
        bookButton.textContent = 'Book Now';
      }
    };

    xhr.send(formData);
  });
</script>

<?php include 'includes/footer.php'; ?>