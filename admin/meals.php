<?php include 'includes/header.php'; ?>

<head>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<h2>Meal Management</h2>

<div class="action-buttons">
  <button id="add-meal-btn">Add New Meal</button>
</div>

<div id="add-meal-form" style="display: none;">
  <h3>Add New Meal</h3>
  <form id="mealForm" method="post" action="actions/add_meal.php">
    <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div>
      <label for="description">Description:</label>
      <textarea id="description" name="description" required></textarea>
    </div>
    <div>
      <label for="price">Price:</label>
      <input type="number" id="price" name="price" step="0.01" required>
    </div>
    <div>
      <button type="submit">Save Meal</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-meal-form" style="display: none;">
  <h3>Edit Meal</h3>
  <form id="editMealForm" method="post" action="actions/edit_meal.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_name">Name:</label>
      <input type="text" id="edit_name" name="name" required>
    </div>
    <div>
      <label for="edit_description">Description:</label>
      <textarea id="edit_description" name="description" required></textarea>
    </div>
    <div>
      <label for="edit_price">Price:</label>
      <input type="number" id="edit_price" name="price" step="0.01" required>
    </div>
    <div>
      <button type="submit">Update Meal</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="meals-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Price</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM meals ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>$" . $row['price'] . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='5'>No meals found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new meal
    document.getElementById('add-meal-btn').addEventListener('click', function() {
      document.getElementById('add-meal-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-meal-form').style.display = 'none';
    });

    // Edit meal
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const mealId = this.getAttribute('data-id');
        fetchMealDetails(mealId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-meal-form').style.display = 'none';
    });

    // Delete meal
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this meal?')) {
          const mealId = this.getAttribute('data-id');
          deleteMeal(mealId);
        }
      });
    });
  });

  function fetchMealDetails(id) {
    fetch('actions/get_meal.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_description').value = data.description;
        document.getElementById('edit_price').value = data.price;

        document.getElementById('edit-meal-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteMeal(id) {
    fetch('actions/delete_meal.php', {
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
          alert('Failed to delete meal: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
  }
</script>

<?php include 'includes/footer.php'; ?>