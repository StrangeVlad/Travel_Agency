<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agence_voyage2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get tour type from URL parameter (default to showing all if not specified)
$tour_type = isset($_GET['type']) ? $_GET['type'] : '';

// Prepare the WHERE clause based on tour type
$where_clause = "";
if ($tour_type === 'National' || $tour_type === 'International') {
    $where_clause = "WHERE tour_type = '$tour_type'";
}

// Query to fetch destinations based on tour type
$sql_destinations = "SELECT * FROM destinations $where_clause ORDER BY country_name";
$result_destinations = $conn->query($sql_destinations);

// Get destination ID from URL parameter for hotel display
$destination_id = isset($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Prepare hotel query if a destination is selected
$hotels = [];
if ($destination_id > 0) {
    $sql_hotels = "SELECT * FROM hotels WHERE destination_id = $destination_id ORDER BY name";
    $result_hotels = $conn->query($sql_hotels);

    if ($result_hotels && $result_hotels->num_rows > 0) {
        while ($row = $result_hotels->fetch_assoc()) {
            $hotels[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agence De Voyage - Destinations</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .destinations {
            text-align: center;

        }

        /* Additional CSS for the destination and hotel displays */
        .destination-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;


        }

        .container-dest {
            background-color: #f8f9fa;
            padding: 6rem 2rem;
            border-top: 3px solid #007BFF;
        }

        .container-dest h2 {
            font-size: 2.5rem;
            color: #007BFF;
            margin-bottom: 3rem;
            font-weight: bold;
        }

        .destination-card {
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }


        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .destination-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 3px solid #007BFF;
        }

        .destination-card-body {
            padding: 15px;
        }

        .destination-card h3 {
            font-size: 1.8rem;
            color: #007BFF;
            margin: 1rem 0;
            font-weight: bold;
        }

        .destination-card p {
            padding: 0 1rem 1.5rem;
            font-size: 1rem;
            color: #555;
            line-height: 1.5;
        }

        .destination-card .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .destination-card .btn:hover {
            background-color: #45a049;
        }

        .hotel-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .hotel-card {
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .hotel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 3px solid #007BFF;
        }

        .hotel-details {
            padding: 15px;
        }

        .hotel-name {
            font-size: 1.8rem;
            color: #007BFF;
            margin: 1rem 0;
            font-weight: bold;
        }

        .hotel-location,
        .hotel-price,
        .hotel-rating,
        .hotel-description {
            color: #666;
            margin-bottom: 15px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .hotel-price {
            font-weight: bold;
            color: #e74c3c;
        }

        .hotel-rating {
            color: #f39c12;
        }

        .hotel-card .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .hotel-card .btn:hover {
            background-color: #45a049;
        }


        .type-selector {
            text-align: center;
            margin: 20px 0;
        }

        .type-selector a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .type-selector a:hover,
        .type-selector a.active {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            margin: 20px 0;
            text-align: center;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .popup-content {
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 90%;
            padding: 30px;
            position: relative;
            overflow-y: auto;
            max-height: 90vh;
        }

        .popup-content h2 {
            color: #007BFF;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            color: #333;
            cursor: pointer;
        }

        .popup-content form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        .popup-content form input,
        .popup-content form select {
            width: 100%;
            padding: 8px 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .btn-confirm {
            display: block;
            margin-top: 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-confirm:hover {
            background-color: #45a049;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Modal Box */
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        /* Close Button */
        .modal-close {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 22px;
            color: #333;
            cursor: pointer;
        }

        /* Buttons */
        .modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-btn {
            background-color: #007BFF;
            color: #fff;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        .register-btn {
            background-color: #28a745;
            color: #fff;
        }

        .register-btn:hover {
            background-color: #1e7e34;
        }

        .right {
            position: absolute;
            right: 10px;
            top: 10px;
            z-index: 1000;
            background-color: #4CAF50;
            padding: 5px 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);

        }

        /* Popup styles will be used from your existing CSS */
    </style>
    <script>
        function openBookingPopup(hotelId, hotelName, hotelLocation, hotelPrice, hotelRating, destinationId) {
            document.getElementById('hotel-details-section').style.display = 'flex';
            document.getElementById('hotel-name').textContent = hotelName;
            document.getElementById('hotel-location').textContent = hotelLocation;
            document.getElementById('hotel-price').textContent = '$' + hotelPrice + ' USD';
            document.getElementById('hotel-rating').textContent = hotelRating + '/5';

            // Set form values
            document.getElementById('hotel-id').value = hotelId;
            document.getElementById('destination-id').value = destinationId;

            // Set minimum dates for check-in and check-out
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            const todayFormatted = today.toISOString().split('T')[0];
            const tomorrowFormatted = tomorrow.toISOString().split('T')[0];

            document.getElementById('checkin').min = todayFormatted;
            document.getElementById('checkout').min = tomorrowFormatted;

            // Reset form
            document.getElementById('booking-form').reset();

            // Load hotel gallery and services
            loadHotelGallery(hotelId);
            loadHotelServices(hotelId);
        }

        function closeBookingPopup() {
            document.getElementById('hotel-details-section').style.display = 'none';
        }

        function showBaridiLink() {
            const paymentMethod = document.getElementById('payment').value;
            document.getElementById('baridi-link').style.display = (paymentMethod === 'baridi') ? 'block' : 'none';
        }

        function loadHotelGallery(hotelId) {
            // This would typically be an AJAX call to get hotel images
            // For now we'll simulate it with placeholder content
            const galleryElement = document.getElementById('hotel-gallery');
            galleryElement.innerHTML = '<p>Loading gallery...</p>';

            // Simulating AJAX with setTimeout
            setTimeout(() => {
                galleryElement.innerHTML = `
                    <div class="gallery-item">
                        <img src="Photo/hotel-placeholder.jpg" alt="Hotel image">
                    </div>
                    <div class="gallery-item">
                        <img src="Photo/hotel-placeholder.jpg" alt="Hotel image">
                    </div>
                `;
            }, 500);
        }

        function loadHotelServices(hotelId) {
            // This would typically be an AJAX call to get hotel services
            // For now we'll simulate it with placeholder content
            const servicesElement = document.getElementById('hotel-services');
            servicesElement.innerHTML = '<p>Loading services...</p>';

            // Simulating AJAX with setTimeout
            setTimeout(() => {
                servicesElement.innerHTML = `
                    <h4>Hotel Services</h4>
                    <ul>
                        <li>Free WiFi</li>
                        <li>Swimming Pool</li>
                        <li>Room Service</li>
                        <li>Restaurant</li>
                    </ul>
                `;
            }, 500);
        }
    </script>
</head>

<body>

    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="agence.html">Home</a></li>
        </ul>
    </nav>

    <div class="logo">
        <img src="Photo/logo.png.jpg" alt="Logo">
    </div>
    <div class="right">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            echo 'Welcome ' . htmlspecialchars($_SESSION['user_name']);
            echo '<br>';
            echo '<a href="logout.php">Logout</a>';
        } else {
            echo 'Guest mode';
            echo '<br>';
            echo '<a href="login.php">Login In</a>';
        }
        ?>
    </div>

    <section id="destinations" class="destinations">
        <div class="container">
            <div class="type-selector">
                <a href="destination.php" <?php echo empty($tour_type) ? 'class="active"' : ''; ?>>All Destinations</a>
                <a href="destination.php?type=National" <?php echo $tour_type === 'National' ? 'class="active"' : ''; ?>>National Tours</a>
                <a href="destination.php?type=International" <?php echo $tour_type === 'International' ? 'class="active"' : ''; ?>>International Tours</a>
                <a href="custompackages.html">Custom Package</a>
            </div>

            <?php if ($destination_id > 0): ?>
                <!-- Show hotels for selected destination -->
                <div class="container-dest">
                    <div class="destination-container">
                        <h2>Hotels in <?php
                                        $sql_dest_name = "SELECT country_name FROM destinations WHERE destination_id = $destination_id";
                                        $result_dest_name = $conn->query($sql_dest_name);
                                        if ($result_dest_name && $result_dest_name->num_rows > 0) {
                                            $dest_name = $result_dest_name->fetch_assoc();
                                            echo htmlspecialchars($dest_name['country_name']);
                                        } else {
                                            echo "Selected Destination";
                                        }
                                        ?></h2>

                        <div class="hotel-container">
                            <?php if (count($hotels) > 0): ?>
                                <?php foreach ($hotels as $hotel): ?>
                                    <div class="hotel-card">
                                        <img src="<?php echo !empty($hotel['image']) ? htmlspecialchars($hotel['image']) : 'Photo/hotel-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                                        <div class="hotel-details">
                                            <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                                            <p class="hotel-location">📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
                                            <p class="hotel-price">💰 $<?php echo htmlspecialchars($hotel['price']); ?> USD per night</p>
                                            <p class="hotel-rating">⭐ Rating: <?php echo htmlspecialchars($hotel['rating']); ?>/5</p>
                                            <p class="hotel-description"><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
                                            <button id="sub" class="btn" onclick="            handleReservationClick(
                <?php echo $hotel['hotel_id']; ?>,
                '<?php echo addslashes($hotel['name']); ?>',
                '<?php echo addslashes($hotel['location']); ?>',
                '<?php echo $hotel['price']; ?>',
                '<?php echo $hotel['rating']; ?>',
                <?php echo $destination_id; ?>
            )">Reserve Now</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No hotels available for this destination yet. Please check back later.</p>
                            <?php endif; ?>
                        </div>

                        <a href="destination.php<?php echo !empty($tour_type) ? '?type=' . urlencode($tour_type) : ''; ?>" class="back-link">← Back to Destinations</a>

                    <?php else: ?>
                        <!-- Show destinations -->
                        <div class="container-dest">
                            <h2>Our Travel Destinations</h2>
                            <div class="destination-container">

                                <?php
                                if ($result_destinations && $result_destinations->num_rows > 0) {
                                    while ($row = $result_destinations->fetch_assoc()) {
                                ?>
                                        <div class="destination-card">
                                            <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'Photo/destination-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($row['country_name']); ?>">
                                            <div class="destination-card-body">
                                                <h3><?php echo htmlspecialchars($row['country_name']); ?></h3>
                                                <p><?php echo htmlspecialchars(substr($row['description'], 0, 100) . '...'); ?></p>
                                                <a href="destination.php?destination_id=<?php echo $row['destination_id']; ?><?php echo !empty($tour_type) ? '&type=' . urlencode($tour_type) : ''; ?>" class="btn">View Hotels</a>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo "<p>No destinations found. Please check back later.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>
    </section>

    <!-- Booking Popup -->
    <section id="hotel-details-section" class="popup-overlay" style="display: none;">

        <div class="popup-content">
            <span class="close-btn" onclick="closeBookingPopup()">&times;</span>
            <h2 id="hotel-name"></h2>

            <div class="hotel-gallery" id="hotel-gallery"></div>
            <div class="hotel-services" id="hotel-services"></div>

            <p><strong>📍 Location:</strong> <span id="hotel-location"></span></p>
            <p><strong>💰 Price per night:</strong> <span id="hotel-price"></span></p>
            <p><strong>⭐ Customer Rating:</strong> <span id="hotel-rating"></span></p>

            <!-- Booking Form -->
            <form id="booking-form" action="save_reservations.php" method="POST">
                <input type="hidden" id="hotel-id" name="hotel_id">
                <input type="hidden" id="destination-id" name="destination_id">

                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" placeholder="+213..." required>

                <label for="birthday">Date of Birth:</label>
                <input type="date" id="birthday" name="birthday" required>

                <label for="passport">Passport Number:</label>
                <input type="text" id="passport" name="passport" placeholder="Enter your passport number" required>

                <label for="checkin">Check-in Date:</label>
                <input type="date" id="checkin" name="checkin" required>

                <label for="checkout">Check-out Date:</label>
                <input type="date" id="checkout" name="checkout" required>

                <label for="guests">Number of Guests:</label>
                <input type="number" id="guests" name="guests" min="1" value="1" required>

                <label for="roomtype">Room Type:</label>
                <select id="roomtype" name="roomtype" required>
                    <option value="">Select Room Type</option>
                    <option value="Single">Single Room</option>
                    <option value="Double">Double Room</option>
                    <option value="Suite">Suite</option>
                    <option value="Family">Family Room</option>
                </select>

                <label for="payment">Payment Method:</label>
                <select id="payment" name="payment" onchange="showBaridiLink()">
                    <option value="card">Credit Card</option>
                    <option value="paypal">Cash</option>
                    <option value="baridi">Baridi Mob</option>
                </select>

                <div id="baridi-link" style="margin-top:10px; display:none;">
                    To pay via <strong>Baridi Mob</strong>, please visit:
                    <a href="https://baridimob.pro/" target="_blank">https://baridimob.pro/</a>
                </div>

                <button type="submit" class="btn-confirm">Confirm Booking</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 SkyLine. All rights reserved.</p>
        </div>
    </footer>
    <!-- Login Required Modal -->
    <div id="loginModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeLoginModal()">&times;</span>
            <h2>Login Required</h2>
            <p>You need to log in or create an account to continue.</p>
            <div class="modal-buttons">
                <a href="login.php" class="modal-btn login-btn">Login</a>
                <a href="register.html" class="modal-btn register-btn">Register</a>
            </div>
        </div>
    </div>

</body>
<script>
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    document.getElementById("sub").addEventListener("click", function() {
        const userId = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";

        if (!userId) {
            // Show custom modal
            document.getElementById("loginModal").style.display = "flex";
        }
    });

    function closeLoginModal() {
        document.getElementById("loginModal").style.display = "none";
    }
</script>


<script>
    function handleReservationClick(hotelId, hotelName, hotelLocation, hotelPrice, hotelRating, destinationId) {
        if (isLoggedIn) {
            openBookingPopup(hotelId, hotelName, hotelLocation, hotelPrice, hotelRating, destinationId);
        } else {
            // Show login modal instead of popup
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.style.display = 'flex';
            } else {
                alert("You must be logged in to reserve. Redirecting to login...");
                window.location.href = "login.php";
            }
        }
    }
</script>

</html>

<?php
// Close the database connection
$conn->close();
?>