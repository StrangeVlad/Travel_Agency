<?php
include('config.php');

if (isset($_GET['destination_id'])) {
    $destination_id = intval($_GET['destination_id']);

    // Get destination name with correct column name
    $dest_result = $conn->query("SELECT country_name FROM destinations WHERE destination_id = $destination_id");
    if (!$dest_result || $dest_result->num_rows === 0) {
        echo "<p>Destination not found.</p>";
        exit;
    }
    $dest = $dest_result->fetch_assoc();

    echo "<h2>Hotels in " . htmlspecialchars($dest['country_name']) . "</h2>";

    $result = $conn->query("SELECT * FROM hotels WHERE destination_id = $destination_id");

    if ($result && $result->num_rows > 0) {
        echo '<div class="hotel-cards">';
        while ($hotel = $result->fetch_assoc()) {
            echo '<div class="hotel-card">';
            echo '<img src="Photo/' . htmlspecialchars($hotel['image']) . '" alt="' . htmlspecialchars($hotel['name']) . '">';
            echo '<h3>' . htmlspecialchars($hotel['name']) . '</h3>';
            echo '<p>' . htmlspecialchars($hotel['description']) . '</p>';
            echo '<p>Price: $' . htmlspecialchars($hotel['price']) . '</p>';
            echo '<p>Rating: ' . htmlspecialchars($hotel['rating']) . ' / 5</p>';
            echo '<button>Book Now</button>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo "<p>No hotels found for this destination.</p>";
    }
} else {
    echo "<p>No destination selected.</p>";
}
?>
  <style>
    * {
        box-sizing: border-box;
      }
  
      body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f4f4;
      }
 /* Services section */
#services {
    background-color: #f8f9fa; /* Light background for better readability */
    padding: 6rem 2rem; /* Increased padding for a spacious layout */
    text-align: center; /* Centered text alignment */
    border-top: 3px solid #007BFF; /* Top border for visual emphasis */
}

#services h2 {
    font-size: 2.5rem;
    color: #007BFF; /* Primary blue color */
    margin-bottom: 3rem;
    font-weight: bold;
}
/* Flexbox layout for service cards */
.service-items {
    display: flex;
    justify-content: center; /* Center all items */
    flex-wrap: wrap;
    gap: 2rem; /* Space between items */
}
/* Individual service item/card */
.service-item {
    background: linear-gradient(145deg, #ffffff, #e6e6e6);  /* Light gradient background */
    border: 1px solid #ddd; /* Light border */
    border-radius: 15px; /* Rounded corners */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Softer shadow */
    padding: 2.5rem; /* Extra padding for balance */
    text-align: center;
    flex: 1 1 calc(30% - 2rem); /* 3 items per row layout */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effects */
}
/* Hover effect for service card */
.service-item:hover {
    transform: translateY(-10px); /* Lift on hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
}
/* Service card title */
.service-item h3 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 1rem;
    font-weight: bold;
}
/* Service card description */
.service-item p {
    font-size: 1rem;
    color: #555;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}
/* Service card button styling */
.service-item .btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    color: #fff;
    background-color: #007BFF;
    text-decoration: none;
    border-radius: 30px; /* Rounded button */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Button hover effects */
}

.service-item .btn:hover {
    background-color: #0056b3;
    transform: scale(1.05); /* Slightly larger button on hover */
}
/* Service image styling */
.service-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 3px solid #007BFF; /* Accent color for images */
}
/* ================National and International sections============= */
.national, .International {
    background-color: #f8f9fa; /* Light background for consistency */
    padding: 6rem 2rem; /* Spacious padding */
    text-align: center;
    border-top: 3px solid #007BFF; /* Section separator */
}
/* Section headings */
.national h1, .International h1 {
    font-size: 2.5rem;
    color: #007BFF;
    margin-bottom: 3rem;
    font-weight: bold;
}
/* Layout for destination cards */
.destination-cards {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 2rem; /* Spacing between cards */
}
/* Individual destination card */
.destination-card {
    background: linear-gradient(145deg, #ffffff, #e6e6e6); /* Subtle gradient for depth */
    border: 1px solid #ddd;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
    overflow: hidden;
    max-width: 300px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.destination-card:hover {
    transform: translateY(-10px); /* Lift effect on hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
}
/* Destination image */
.destination-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 3px solid #007BFF; /* Accent color for images */
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

/* ===============Custom section============== */
.Custom {
    background-color: #fff; /* White background for a clean look */
    padding: 4rem 2rem;
    text-align: center;
    border-top: 3px solid #007BFF;
}

.Custom h1 {
    font-size: 2.5rem;
    color: #007BFF;
    margin-bottom: 2rem;
    font-weight: bold;
}

.Custom p {
    font-size: 1.2rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 2rem;
}

  /* ====Back to Top Button Styles==== */
.back-to-top-btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            color: #fff;
            background-color: #007BFF; /* Same blue as other buttons */
            text-decoration: none;
            border-radius: 30px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 2rem; /* Add some space above the button */
}
 .back-to-top-btn:hover {
background-color: #0056b3;
 transform: scale(1.05);
 }
 /* ===================== Carousel Section ===================== */

 .city-details {
      padding: 40px 20px;
      background-color: #f8f9fa;
    }

    .city-details h2 {
      text-align: center;
      font-size: 2.2em;
      color: #333;
      margin-bottom: 10px;
    }

    .city-details p {
      text-align: center;
      font-size: 1.1em;
      color: #666;
      margin-bottom: 30px;
    }

    .carousel-container {
      position: relative;
      max-width: 1200px;
      margin: auto;
      overflow: hidden;
    }

    .carousel {
      display: flex;
      gap: 20px;
      transition: transform 0.5s ease-in-out;
      overflow-x: auto;
      scroll-behavior: smooth;
      padding-bottom: 20px;
    }

    .card {
      flex: 0 0 auto;
      width: 250px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
      overflow: hidden;
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card img {
      width: 100%;
      height: 170px;
      object-fit: cover;
    }

    .card h3 {
      font-size: 1.1em;
      margin: 15px 0 5px;
      color: #2c3e50;
    }

    .card p {
      color: #888;
      font-size: 0.95em;
      margin-bottom: 10px;
    }

    .card button {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 10px 15px;
      margin-bottom: 15px;
      border-radius: 25px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .card button:hover {
      background-color: #0056b3;
    }

    .carousel-btn {
      position: absolute;
      top: 45%;
      transform: translateY(-50%);
      background: #ffffffcc;
      border: none;
      padding: 10px;
      font-size: 24px;
      cursor: pointer;
      z-index: 1;
      border-radius: 50%;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .carousel-btn:hover {
      background: #fff;
    }

    .carousel-btn.prev {
      left: -10px;
    }

    .carousel-btn.next {
      right: -10px;
    }

    .back-to-top-btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 25px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-to-top-btn:hover {
      background-color: #1e7e34;
    }

    @media (max-width: 768px) {
      .carousel {
        gap: 10px;
      }

      .card {
        width: 200px;
      }

      .carousel-btn {
        display: none;
      }
    }
/* ===================== Popup Window ===================== */
.popup-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    overflow-y: auto;
}
.popup-content {
    background: white;
    padding: 20px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    max-height: 80vh; /* تحديد الحد الأقصى للارتفاع */
    overflow-y: auto; /* السماح بالتمرير إذا تجاوز المحتوى الحجم */
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);

}
.close-btn {
    float: right;
    cursor: pointer;
}
/* Confirmation Message */
.confirmation-container {
    text-align: center;
    margin-top: 50px;
}
/* ===================== Form Styles ===================== */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Input Fields */
input, select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 6px;
    width: 100%;
}
/* Confirm Button */
.btn-confirm {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}
.btn-confirm:hover {
    background: #0056b3;
}
/* Links */
a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
/* ===================== Footer ===================== */
.footer {
    background-color: #a2a8df;
    color: white;
    padding: 1rem 0;
    text-align: center;
    margin-top: 2rem;
    z-index: 1;
}
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
    padding: 20px;
}

/* Popup Content Box */
.popup-content {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    max-width: 600px;
    width: 100%;
    position: relative;
    overflow-y: auto;
    max-height: 95vh;
    box-shadow: 0 0 25px rgba(0,0,0,0.2);
}

/* Close Button */
.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
    color: #333;
}

/* Form Styles */
form label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

form input,
form select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form input:focus,
form select:focus {
    outline: none;
    border-color: #007BFF;
}

/* Confirm Button */
.btn-confirm {
    margin-top: 20px;
    background-color: #28a745;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 10px;
    width: 100%;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-confirm:hover {
    background-color: #218838;
}
/* Hotel Gallery */
.hotel-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
    justify-content: flex;
}

.hotel-gallery img {
    max-width: 120px;
    border-radius: 80px;
    object-fit: cover;
 border-radius: 6px;
 cursor: pointer;
 transition: transform 0.2s ease;
}

.hotel-gallery img:hover {
    transform: scale(1.05);
}

/* Services Section */
.hotel-services {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: flex-start;
}

.hotel-services .service-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 1rem;
    width: 160px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease;
}

.hotel-services .service-box:hover {
    transform: translateY(-5px);
}

.service-box img {
    width: 40px;
    height: 40px;
    margin-bottom: 0.5rem;
}

.service-box p {
    font-size: 0.95rem;
    color: #333;
    margin: 0;
}

/* Overlay Photo Styling */
.overlay-photo {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}

.overlay-photo img {
    display: block;
    width: 100%;
    height: auto;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    font-weight: bold;
    font-size: 1.1rem;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.overlay-photo:hover .overlay {
    opacity: 1;
}

</style>