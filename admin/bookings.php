<?php include 'includes/header.php'; ?>

<head>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<h2>Bookings Management</h2>

<div class="action-buttons">
  <button id="add-booking-btn">Add New Booking</button>
</div>

<div id="add-booking-form" style="display: none;">
  <h3>Add New Booking</h3>
  <form id="bookingForm" method="post" action="actions/add_booking.php">
    <div>
      <label for="customer_name">Customer Name:</label>
      <input type="text" id="customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="customer_email">Customer Email:</label>
      <input type="email" id="customer_email" name="customer_email" required>
    </div>
    <div>
      <label for="hotel_id">Hotel:</label>
      <select id="hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
          }
        }
        ?>
      </select>
    </div>
    <div>
      <label for="check_in_date">Check-in Date:</label>
      <input type="date" id="check_in_date" name="check_in_date" required>
    </div>
    <div>
      <label for="check_out_date">Check-out Date:</label>
      <input type="date" id="check_out_date" name="check_out_date" required>
    </div>
    <div>
      <label for="num_guests">Number of Guests:</label>
      <input type="number" id="num_guests" name="num_guests" min="1" required>
    </div>
    <div>
      <label for="total_price">Total Price:</label>
      <input type="number" id="total_price" name="total_price" min="0" step="0.01" required>
    </div>
    <div>
      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
      </select>
    </div>
    <div>
      <label for="special_requests">Special Requests:</label>
      <textarea id="special_requests" name="special_requests"></textarea>
    </div>
    <div>
      <button type="submit">Save Booking</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-booking-form" style="display: none;">
  <h3>Edit Booking</h3>
  <form id="editBookingForm" method="post" action="actions/edit_booking.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_customer_name">Customer Name:</label>
      <input type="text" id="edit_customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="edit_customer_email">Customer Email:</label>
      <input type="email" id="edit_customer_email" name="customer_email" required>
    </div>
    <div>
      <label for="edit_hotel_id">Hotel:</label>
      <select id="edit_hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
          }
        }
        ?>
      </select>
    </div>
    <div>
      <label for="edit_check_in_date">Check-in Date:</label>
      <input type="date" id="edit_check_in_date" name="check_in_date" required>
    </div>
    <div>
      <label for="edit_check_out_date">Check-out Date:</label>
      <input type="date" id="edit_check_out_date" name="check_out_date" required>
    </div>
    <div>
      <label for="edit_num_guests">Number of Guests:</label>
      <input type="number" id="edit_num_guests" name="num_guests" min="1" required>
    </div>
    <div>
      <label for="edit_total_price">Total Price:</label>
      <input type="number" id="edit_total_price" name="total_price" min="0" step="0.01" required>
    </div>
    <div>
      <label for="edit_status">Status:</label>
      <select id="edit_status" name="status" required>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
      </select>
    </div>
    <div>
      <label for="edit_special_requests">Special Requests:</label>
      <textarea id="edit_special_requests" name="special_requests"></textarea>
    </div>
    <div>
      <button type="submit">Update Booking</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="bookings-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Customer</th>
      <th>Hotel</th>
      <th>Check-in</th>
      <th>Check-out</th>
      <th>Guests</th>
      <th>Price</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT b.*, h.name as hotel_name 
                FROM bookings b 
                LEFT JOIN hotels h ON b.id = h.id 
                ORDER BY b.id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['hotel_name'] . "</td>";
        echo "<td>" . $row['check_in_date'] . "</td>";
        echo "<td>" . $row['check_out_date'] . "</td>";
        echo "<td>" . $row['num_guests'] . "</td>";
        echo "<td>$" . number_format($row['total_price'], 2) . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='9'>No bookings found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new booking
    document.getElementById('add-booking-btn').addEventListener('click', function() {
      document.getElementById('add-booking-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-booking-form').style.display = 'none';
    });

    // Date validation for add form
    document.getElementById('check_in_date').addEventListener('change', validateDates);
    document.getElementById('check_out_date').addEventListener('change', validateDates);

    // Date validation for edit form
    document.getElementById('edit_check_in_date').addEventListener('change', validateEditDates);
    document.getElementById('edit_check_out_date').addEventListener('change', validateEditDates);

    // Edit booking
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const bookingId = this.getAttribute('data-id');
        fetchBookingDetails(bookingId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-booking-form').style.display = 'none';
    });

    // Delete booking
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this booking?')) {
          const bookingId = this.getAttribute('data-id');
          deleteBooking(bookingId);
        }
      });
    });

    // Form submissions
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
      e.preventDefault();

      if (!validateDates()) {
        return false;
      }

      const formData = new FormData(this);

      fetch('actions/add_booking.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Booking added successfully!');
            window.location.reload();
          } else {
            alert('Error adding booking: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while adding the booking.');
        });
    });

    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
      e.preventDefault();

      if (!validateEditDates()) {
        return false;
      }

      const formData = new FormData(this);

      fetch('actions/edit_booking.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Booking updated successfully!');
            window.location.reload();
          } else {
            alert('Error updating booking: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the booking.');
        });
    });
  });

  function validateDates() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;

    if (checkIn && checkOut && checkIn >= checkOut) {
      alert('Check-out date must be after check-in date');
      document.getElementById('check_out_date').value = '';
      return false;
    }
    return true;
  }

  function validateEditDates() {
    const checkIn = document.getElementById('edit_check_in_date').value;
    const checkOut = document.getElementById('edit_check_out_date').value;

    if (checkIn && checkOut && checkIn >= checkOut) {
      alert('Check-out date must be after check-in date');
      document.getElementById('edit_check_out_date').value = '';
      return false;
    }
    return true;
  }

  function fetchBookingDetails(id) {
    fetch('actions/get_booking.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_customer_name').value = data.customer_name;
        document.getElementById('edit_customer_email').value = data.customer_email;
        document.getElementById('edit_hotel_id').value = data.hotel_id;
        document.getElementById('edit_check_in_date').value = data.check_in_date;
        document.getElementById('edit_check_out_date').value = data.check_out_date;
        document.getElementById('edit_num_guests').value = data.num_guests;
        document.getElementById('edit_total_price').value = data.total_price;
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_special_requests').value = data.special_requests;

        document.getElementById('edit-booking-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteBooking(id) {
    fetch('actions/delete_booking.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove the row from the table
          const row = document.querySelector(`tr[data-id="${id}"]`);
          row.parentNode.removeChild(row);
        } else {
          alert('Error deleting booking: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the booking.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>