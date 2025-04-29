<?php include 'includes/header.php'; ?>

<head>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<h2>Hotel Room Management</h2>

<div class="action-buttons">
  <button id="add-room-btn">Add New Room</button>
</div>

<div id="add-room-form" style="display: none;">
  <h3>Add New Hotel Room</h3>
  <form id="roomForm" method="post" action="actions/add_hotel_room.php">
    <div>
      <label for="hotel_id">Hotel:</label>
      <select id="hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name";
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
      <label for="room_type">Room Type:</label>
      <input type="text" id="room_type" name="room_type" required>
    </div>
    <div>
      <label for="price_per_night">Price Per Night:</label>
      <input type="number" id="price_per_night" name="price_per_night" step="0.01" required>
    </div>
    <div>
      <label for="total_rooms">Total Rooms:</label>
      <input type="number" id="total_rooms" name="total_rooms" required>
    </div>
    <div>
      <label for="available_rooms">Available Rooms:</label>
      <input type="number" id="available_rooms" name="available_rooms" required>
    </div>
    <div>
      <button type="submit">Save Room</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-room-form" style="display: none;">
  <h3>Edit Hotel Room</h3>
  <form id="editRoomForm" method="post" action="actions/edit_hotel_room.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_hotel_id">Hotel:</label>
      <select id="edit_hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name";
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
      <label for="edit_room_type">Room Type:</label>
      <input type="text" id="edit_room_type" name="room_type" required>
    </div>
    <div>
      <label for="edit_price_per_night">Price Per Night:</label>
      <input type="number" id="edit_price_per_night" name="price_per_night" step="0.01" required>
    </div>
    <div>
      <label for="edit_total_rooms">Total Rooms:</label>
      <input type="number" id="edit_total_rooms" name="total_rooms" required>
    </div>
    <div>
      <label for="edit_available_rooms">Available Rooms:</label>
      <input type="number" id="edit_available_rooms" name="available_rooms" required>
    </div>
    <div>
      <button type="submit">Update Room</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="rooms-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Hotel</th>
      <th>Room Type</th>
      <th>Price Per Night</th>
      <th>Available Rooms</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT hr.id, h.name as hotel_name, hr.room_type, hr.price_per_night, 
                hr.available_rooms, hr.total_rooms
                FROM hotel_rooms hr
                JOIN hotels h ON hr.hotel_id = h.id
                ORDER BY h.name, hr.room_type";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['hotel_name'] . "</td>";
        echo "<td>" . $row['room_type'] . "</td>";
        echo "<td>$" . $row['price_per_night'] . "</td>";
        echo "<td>" . $row['available_rooms'] . "/" . $row['total_rooms'] . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='6'>No hotel rooms found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new room
    document.getElementById('add-room-btn').addEventListener('click', function() {
      document.getElementById('add-room-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-room-form').style.display = 'none';
    });

    // Edit room
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const roomId = this.getAttribute('data-id');
        fetchRoomDetails(roomId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-room-form').style.display = 'none';
    });

    // Delete room
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this room?')) {
          const roomId = this.getAttribute('data-id');
          deleteRoom(roomId);
        }
      });
    });
  });

  function fetchRoomDetails(id) {
    fetch('actions/get_hotel_room.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_hotel_id').value = data.hotel_id;
        document.getElementById('edit_room_type').value = data.room_type;
        document.getElementById('edit_price_per_night').value = data.price_per_night;
        document.getElementById('edit_total_rooms').value = data.total_rooms;
        document.getElementById('edit_available_rooms').value = data.available_rooms;

        document.getElementById('edit-room-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteRoom(id) {
    fetch('actions/delete_hotel_room.php', {
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
          alert('Failed to delete room: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
  }
</script>

<?php include 'includes/footer.php'; ?>