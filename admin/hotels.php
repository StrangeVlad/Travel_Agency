<?php include 'includes/header.php'; ?>

<head>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<h2>Hotel Management</h2>

<div class="action-buttons">
  <button id="add-hotel-btn">Add New Hotel</button>
</div>

<div id="add-hotel-form" style="display: none;">
  <h3>Add New Hotel</h3>
  <form id="hotelForm" method="post" action="actions/add_hotel.php">
    <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div>
      <label for="destination">Destination:</label>
      <input type="text" id="destination" name="destination" required>
    </div>
    <div>
      <label for="address">Address:</label>
      <textarea id="address" name="address" required></textarea>
    </div>
    <div>
      <label for="rating">Rating (0-5):</label>
      <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" required>
    </div>
    <div>
      <button type="submit">Save Hotel</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-hotel-form" style="display: none;">
  <h3>Edit Hotel</h3>
  <form id="editHotelForm" method="post" action="actions/edit_hotel.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_name">Name:</label>
      <input type="text" id="edit_name" name="name" required>
    </div>
    <div>
      <label for="edit_destination">Destination:</label>
      <input type="text" id="edit_destination" name="destination" required>
    </div>
    <div>
      <label for="edit_address">Address:</label>
      <textarea id="edit_address" name="address" required></textarea>
    </div>
    <div>
      <label for="edit_rating">Rating (0-5):</label>
      <input type="number" id="edit_rating" name="rating" min="0" max="5" step="0.1" required>
    </div>
    <div>
      <button type="submit">Update Hotel</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="hotels-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Destination</th>
      <th>Address</th>
      <th>Rating</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM hotels ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['destination'] . "</td>";
        echo "<td>" . $row['address'] . "</td>";
        echo "<td>" . $row['rating'] . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='6'>No hotels found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new hotel
    document.getElementById('add-hotel-btn').addEventListener('click', function() {
      document.getElementById('add-hotel-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-hotel-form').style.display = 'none';
    });

    // Edit hotel
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const hotelId = this.getAttribute('data-id');
        fetchHotelDetails(hotelId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-hotel-form').style.display = 'none';
    });

    // Delete hotel
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this hotel?')) {
          const hotelId = this.getAttribute('data-id');
          deleteHotel(hotelId);
        }
      });
    });
  });

  function fetchHotelDetails(id) {
    fetch('actions/get_hotel.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_destination').value = data.destination;
        document.getElementById('edit_address').value = data.address;
        document.getElementById('edit_rating').value = data.rating;

        document.getElementById('edit-hotel-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteHotel(id) {
    fetch('actions/delete_hotel.php', {
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
          alert('Error deleting hotel: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the hotel.');
      });
  }

  // Form submissions
  document.getElementById('hotelForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('actions/add_hotel.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Hotel added successfully!');
          // Reload the page to show the new hotel
          window.location.reload();
        } else {
          alert('Error adding hotel: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the hotel.');
      });
  });

  document.getElementById('editHotelForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('actions/edit_hotel.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Hotel updated successfully!');
          // Reload the page to show the changes
          window.location.reload();
        } else {
          alert('Error updating hotel: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the hotel.');
      });
  });
</script>

<?php include 'includes/footer.php'; ?>