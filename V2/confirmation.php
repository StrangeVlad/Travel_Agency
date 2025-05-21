<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to view your reservation confirmation.");
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$get_user = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$get_user->bind_param("i", $user_id);
$get_user->execute();
$user_result = $get_user->get_result();
$user = $user_result->fetch_assoc();

$query = $conn->prepare("
    SELECT r.*, h.name AS hotel_name, d.country_name AS destination_name 
    FROM reservations r
    JOIN hotels h ON r.hotel_id = h.hotel_id 
    JOIN destinations d ON r.destination_id = d.destination_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "❌ No reservation found for this user.";
    exit;
}

$reservation = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmation</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f2f2f2;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            color: green;
            text-align: center;
        }

        h3 {
            color: #007BFF;
            margin-top: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
            padding: 10px;
            background: #fafafa;
            border-left: 4px solid #007BFF;
            border-radius: 4px;
        }

        button,
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover,
        a:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>✅ Thank you! Your reservation is confirmed.</h1>

        <h3>User Information:</h3>
        <ul>
            <li><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></li>
            <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
        </ul>

        <h3>Reservation Details:</h3>
        <ul>
            <li><strong>Hotel:</strong> <?php echo htmlspecialchars($reservation['hotel_name']); ?></li>
            <li><strong>Destination:</strong> <?php echo htmlspecialchars($reservation['destination_name']); ?></li>
            <li><strong>Phone:</strong> <?php echo htmlspecialchars($reservation['phone']); ?></li>
            <li><strong>Check-in:</strong> <?php echo htmlspecialchars($reservation['checkin_date']); ?></li>
            <li><strong>Check-out:</strong> <?php echo htmlspecialchars($reservation['checkout_date']); ?></li>
            <li><strong>Guests:</strong> <?php echo htmlspecialchars($reservation['guests']); ?></li>
            <li><strong>Room Type:</strong> <?php echo htmlspecialchars($reservation['room_type']); ?></li>
            <li><strong>Payment Method:</strong> <?php echo htmlspecialchars($reservation['payment_method']); ?></li>
            <li><strong>Reservation Date:</strong> <?php echo htmlspecialchars($reservation['created_at']); ?></li>
        </ul>

        <button onclick="generatePDF()">📄 Download PDF Confirmation</button>
        <a href="agence.html">⬅️ Back to Home</a>
    </div>

    <script>
        function generatePDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            // Replace this with your actual Base64 logo data
            const logoBase64 = "Photo/logo.png.jpg"; // shortened for demo

            // Add the logo to the top left (x=10, y=10), size (width=40, height=20)
            doc.addImage(logoBase64, 'PNG', 150, 10, 40, 20);

            let y = 35;

            // 🧾 Title
            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.text("Reservation Confirmation", 105, y, {
                align: "center"
            });

            y += 15;

            //  User Information
            doc.setFontSize(14);
            doc.setFont("helvetica", "bold");
            doc.text("User Information:", 10, y);
            doc.setFont("helvetica", "normal");
            y += 8;
            doc.text("Name: <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>", 10, y);
            y += 8;
            doc.text("Email: <?php echo $user['email']; ?>", 10, y);

            //  Reservation Details
            y += 15;
            doc.setFont("helvetica", "bold");
            doc.text("Reservation Details:", 10, y);
            doc.setFont("helvetica", "normal");
            y += 8;
            doc.text("Hotel: <?php echo $reservation['hotel_name']; ?>", 10, y);
            y += 8;
            doc.text("Destination: <?php echo $reservation['destination_name']; ?>", 10, y);
            y += 8;
            doc.text("Phone: <?php echo $reservation['phone']; ?>", 10, y);
            y += 8;
            doc.text("Check-in Date: <?php echo $reservation['checkin_date']; ?>", 10, y);
            y += 8;
            doc.text("Check-out Date: <?php echo $reservation['checkout_date']; ?>", 10, y);
            y += 8;
            doc.text("Guests: <?php echo $reservation['guests']; ?>", 10, y);
            y += 8;
            doc.text("Room Type: <?php echo $reservation['room_type']; ?>", 10, y);
            y += 8;
            doc.text("Payment Method: <?php echo $reservation['payment_method']; ?>", 10, y);
            y += 8;
            doc.text("Reservation Date: <?php echo $reservation['created_at']; ?>", 10, y);
            doc.save("Reservation_Confirmation.pdf");
        }

        // Uncomment if you want to auto-download:
        // window.onload = generatePDF;
    </script>
</body>

</html>