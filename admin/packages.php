<?php include 'includes/header.php'; ?>

<h2>Package Management</h2>

<div class="action-buttons">
  <button id="add-package-btn">Add New Package</button>
</div>

<div id="add-package-form" style="display: none;">
  <h3>Add New Package</h3>

  <form id="packageForm" action="actions/add_package.php" method="POST" enctype="multipart/form-data">

    <div>
      <label for="title">Title:</label>
      <input type="text" id="title" name="title" required>
    </div>
    <div>
      <label for="destination">Destination:</label>
      <input type="text" id="destination" name="destination" required>
    </div>
    <div>
      <label for="description">Description:</label>
      <textarea id="description" name="description" required></textarea>
    </div>
    <div>
      <label for="start_date">Start Date:</label>
      <input type="date" id="start_date" name="start_date" required>
    </div>
    <div>
      <label for="end_date">End Date:</label>
      <input type="date" id="end_date" name="end_date" required>
    </div>
    <div>
      <label for="price">Price:</label>
      <input type="number" id="price" name="price" step="0.01" required>
    </div>
    <div>
      <label for="total_slots">Total Slots:</label>
      <input type="number" id="total_slots" name="total_slots" required>
    </div>
    <div>
      <label for="image">Package Image:</label>
      <input type="file" name="image" accept="image/*" required>
    </div>
    <div>
      <label for="available_slots">Available Slots:</label>
      <input type="number" id="available_slots" name="available_slots" required>
    </div>
    <div>
      <button type="submit">Save Package</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>


  </form>
</div>

<div id="edit-package-form" style="display: none;">
  <h3>Edit Package</h3>
  <form id="editPackageForm" method="post" action="actions/edit_package.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_title">Title:</label>
      <input type="text" id="edit_title" name="title" required>
    </div>
    <div>
      <label for="edit_destination">Destination:</label>
      <input type="text" id="edit_destination" name="destination" required>
    </div>
    <div>
      <label for="edit_description">Description:</label>
      <textarea id="edit_description" name="description" required></textarea>
    </div>
    <div>
      <label for="edit_start_date">Start Date:</label>
      <input type="date" id="edit_start_date" name="start_date" required>
    </div>
    <div>
      <label for="edit_end_date">End Date:</label>
      <input type="date" id="edit_end_date" name="end_date" required>
    </div>
    <div>
      <label for="edit_price">Price:</label>
      <input type="number" id="edit_price" name="price" step="0.01" required>
    </div>
    <div>
      <label for="edit_total_slots">Total Slots:</label>
      <input type="number" id="edit_total_slots" name="total_slots" required>
    </div>
    <div>
      <label for="edit_available_slots">Available Slots:</label>
      <input type="number" id="edit_available_slots" name="available_slots" required>
    </div>
    <div>
      <button type="submit">Update Package</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="packages-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Destination</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Price</th>
      <th>Available Slots</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM packages ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['destination'] . "</td>";
        echo "<td>" . $row['start_date'] . "</td>";
        echo "<td>" . $row['end_date'] . "</td>";
        echo "<td>$" . $row['price'] . "</td>";
        echo "<td>" . $row['available_slots'] . "/" . $row['total_slots'] . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='8'>No packages found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new package
    document.getElementById('add-package-btn').addEventListener('click', function() {
      document.getElementById('add-package-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-package-form').style.display = 'none';
    });

    // Edit package
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const packageId = this.getAttribute('data-id');
        fetchPackageDetails(packageId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-package-form').style.display = 'none';
    });

    // Delete package
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this package?')) {
          const packageId = this.getAttribute('data-id');
          deletePackage(packageId);
        }
      });
    });
  });

  function fetchPackageDetails(id) {
    fetch('actions/get_package.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_title').value = data.title;
        document.getElementById('edit_destination').value = data.destination;
        document.getElementById('edit_description').value = data.description;
        document.getElementById('edit_start_date').value = data.start_date;
        document.getElementById('edit_end_date').value = data.end_date;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_total_slots').value = data.total_slots;
        document.getElementById('edit_available_slots').value = data.available_slots;

        document.getElementById('edit-package-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deletePackage(id) {
    fetch('actions/delete_package.php', {
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
          alert('Failed to delete package: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
  }
</script>

<?php include 'includes/footer.php'; ?>