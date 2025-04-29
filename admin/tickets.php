<?php include 'includes/header.php'; ?>

<h2>Travel Tickets Management</h2>

<div class="action-buttons">
  <button id="add-ticket-btn">Add New Ticket</button>
</div>

<div id="add-ticket-form" style="display: none;">
  <h3>Add New Ticket</h3>
  <form id="ticketForm" method="post" action="actions/add_ticket.php">
    <div>
      <label for="customer_name">Customer Name:</label>
      <input type="text" id="customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="ticket_type">Ticket Type:</label>
      <select id="ticket_type" name="ticket_type" required>
        <option value="">Select Type</option>
        <option value="flight">Flight</option>
        <option value="train">Train</option>
        <option value="bus">Bus</option>
        <option value="ferry">Ferry</option>
      </select>
    </div>
    <div>
      <label for="origin">Origin:</label>
      <input type="text" id="origin" name="origin" required>
    </div>
    <div>
      <label for="destination">Destination:</label>
      <input type="text" id="destination" name="destination" required>
    </div>
    <div>
      <label for="departure_date">Departure Date:</label>
      <input type="date" id="departure_date" name="departure_date" required>
    </div>
    <div>
      <label for="departure_time">Departure Time:</label>
      <input type="time" id="departure_time" name="departure_time" required>
    </div>
    <div>
      <label for="arrival_date">Arrival Date:</label>
      <input type="date" id="arrival_date" name="arrival_date" required>
    </div>
    <div>
      <label for="arrival_time">Arrival Time:</label>
      <input type="time" id="arrival_time" name="arrival_time" required>
    </div>
    <div>
      <label for="seat_number">Seat Number:</label>
      <input type="text" id="seat_number" name="seat_number">
    </div>
    <div>
      <label for="price">Price:</label>
      <input type="number" id="price" name="price" min="0" step="0.01" required>
    </div>
    <div>
      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="booked">Booked</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
      </select>
    </div>
    <div>
      <button type="submit">Save Ticket</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-ticket-form" style="display: none;">
  <h3>Edit Ticket</h3>
  <form id="editTicketForm" method="post" action="actions/edit_ticket.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_customer_name">Customer Name:</label>
      <input type="text" id="edit_customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="edit_ticket_type">Ticket Type:</label>
      <select id="edit_ticket_type" name="ticket_type" required>
        <option value="">Select Type</option>
        <option value="flight">Flight</option>
        <option value="train">Train</option>
        <option value="bus">Bus</option>
        <option value="ferry">Ferry</option>
      </select>
    </div>
    <div>
      <label for="edit_origin">Origin:</label>
      <input type="text" id="edit_origin" name="origin" required>
    </div>
    <div>
      <label for="edit_destination">Destination:</label>
      <input type="text" id="edit_destination" name="destination" required>
    </div>
    <div>
      <label for="edit_departure_date">Departure Date:</label>
      <input type="date" id="edit_departure_date" name="departure_date" required>
    </div>
    <div>
      <label for="edit_departure_time">Departure Time:</label>
      <input type="time" id="edit_departure_time" name="departure_time" required>
    </div>
    <div>
      <label for="edit_arrival_date">Arrival Date:</label>
      <input type="date" id="edit_arrival_date" name="arrival_date" required>
    </div>
    <div>
      <label for="edit_arrival_time">Arrival Time:</label>
      <input type="time" id="edit_arrival_time" name="arrival_time" required>
    </div>
    <div>
      <label for="edit_seat_number">Seat Number:</label>
      <input type="text" id="edit_seat_number" name="seat_number">
    </div>
    <div>
      <label for="edit_price">Price:</label>
      <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
    </div>
    <div>
      <label for="edit_status">Status:</label>
      <select id="edit_status" name="status" required>
        <option value="booked">Booked</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
      </select>
    </div>
    <div>
      <button type="submit">Update Ticket</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="tickets-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Customer</th>
      <th>Type</th>
      <th>Route</th>
      <th>Departure</th>
      <th>Arrival</th>
      <th>Seat</th>
      <th>Price</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM tickets ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . ucfirst($row['ticket_type']) . "</td>";
        echo "<td>" . $row['origin'] . " → " . $row['destination'] . "</td>";
        echo "<td>" . $row['departure_date'] . " " . $row['departure_time'] . "</td>";
        echo "<td>" . $row['arrival_date'] . " " . $row['arrival_time'] . "</td>";
        echo "<td>" . ($row['seat_number'] ? $row['seat_number'] : "N/A") . "</td>";
        echo "<td>$" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='10'>No tickets found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new ticket
    document.getElementById('add-ticket-btn').addEventListener('click', function() {
      document.getElementById('add-ticket-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-ticket-form').style.display = 'none';
    });

    // Date/time validation for add form
    document.getElementById('departure_date').addEventListener('change', validateDates);
    document.getElementById('arrival_date').addEventListener('change', validateDates);

    // Date/time validation for edit form
    document.getElementById('edit_departure_date').addEventListener('change', validateEditDates);
    document.getElementById('edit_arrival_date').addEventListener('change', validateEditDates);

    // Edit ticket
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const ticketId = this.getAttribute('data-id');
        fetchTicketDetails(ticketId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-ticket-form').style.display = 'none';
    });

    // Delete ticket
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this ticket?')) {
          const ticketId = this.getAttribute('data-id');
          deleteTicket(ticketId);
        }
      });
    });

    // Form submissions
    document.getElementById('ticketForm').addEventListener('submit', function(e) {
      e.preventDefault();

      if (!validateDates()) {
        return false;
      }

      const formData = new FormData(this);

      fetch('actions/add_ticket.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Ticket added successfully!');
            window.location.reload();
          } else {
            alert('Error adding ticket: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while adding the ticket.');
        });
    });

    document.getElementById('editTicketForm').addEventListener('submit', function(e) {
      e.preventDefault();

      if (!validateEditDates()) {
        return false;
      }

      const formData = new FormData(this);

      fetch('actions/edit_ticket.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Ticket updated successfully!');
            window.location.reload();
          } else {
            alert('Error updating ticket: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the ticket.');
        });
    });
  });

  function validateDates() {
    const departureDate = document.getElementById('departure_date').value;
    const arrivalDate = document.getElementById('arrival_date').value;

    if (departureDate && arrivalDate) {
      const deptDateTime = new Date(departureDate + 'T' + (document.getElementById('departure_time').value || '00:00'));
      const arrDateTime = new Date(arrivalDate + 'T' + (document.getElementById('arrival_time').value || '00:00'));

      if (deptDateTime > arrDateTime) {
        alert('Arrival date/time must be after departure date/time');
        document.getElementById('arrival_date').value = '';
        document.getElementById('arrival_time').value = '';
        return false;
      }
    }
    return true;
  }

  function validateEditDates() {
    const departureDate = document.getElementById('edit_departure_date').value;
    const arrivalDate = document.getElementById('edit_arrival_date').value;

    if (departureDate && arrivalDate) {
      const deptDateTime = new Date(departureDate + 'T' + (document.getElementById('edit_departure_time').value || '00:00'));
      const arrDateTime = new Date(arrivalDate + 'T' + (document.getElementById('edit_arrival_time').value || '00:00'));

      if (deptDateTime > arrDateTime) {
        alert('Arrival date/time must be after departure date/time');
        document.getElementById('edit_arrival_date').value = '';
        document.getElementById('edit_arrival_time').value = '';
        return false;
      }
    }
    return true;
  }

  function fetchTicketDetails(id) {
    fetch('actions/get_ticket.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_customer_name').value = data.customer_name;
        document.getElementById('edit_ticket_type').value = data.ticket_type;
        document.getElementById('edit_origin').value = data.origin;
        document.getElementById('edit_destination').value = data.destination;
        document.getElementById('edit_departure_date').value = data.departure_date;
        document.getElementById('edit_departure_time').value = data.departure_time;
        document.getElementById('edit_arrival_date').value = data.arrival_date;
        document.getElementById('edit_arrival_time').value = data.arrival_time;
        document.getElementById('edit_seat_number').value = data.seat_number;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_status').value = data.status;

        document.getElementById('edit-ticket-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteTicket(id) {
    fetch('actions/delete_ticket.php', {
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
          alert('Error deleting ticket: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the ticket.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>