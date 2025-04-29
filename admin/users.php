<?php include 'includes/header.php'; ?>

<h2>Users Management</h2>

<div class="action-buttons">
  <button id="add-user-btn">Add New User</button>
</div>

<div id="add-user-form" style="display: none;">
  <h3>Add New User</h3>
  <form id="userForm" method="post" action="actions/add_user.php">
    <div>
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" required>
    </div>
    <div>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div>
      <label for="confirm_password">Confirm Password:</label>
      <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <div>
      <label for="role">Role:</label>
      <select id="role" name="role" required>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="staff">Staff</option>
      </select>
    </div>
    <div>
      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div>
      <button type="submit">Save User</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-user-form" style="display: none;">
  <h3>Edit User</h3>
  <form id="editUserForm" method="post" action="actions/edit_user.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_full_name">Full Name:</label>
      <input type="text" id="edit_full_name" name="full_name" required>
    </div>
    <div>
      <label for="edit_email">Email:</label>
      <input type="email" id="edit_email" name="email" required>
    </div>
    <div>
      <label for="edit_username">Username:</label>
      <input type="text" id="edit_username" name="username" required>
    </div>
    <div>
      <label for="edit_password">Password:</label>
      <input type="password" id="edit_password" name="password" placeholder="Leave blank to keep current password">
    </div>
    <div>
      <label for="edit_confirm_password">Confirm Password:</label>
      <input type="password" id="edit_confirm_password" name="confirm_password" placeholder="Leave blank to keep current password">
    </div>
    <div>
      <label for="edit_role">Role:</label>
      <select id="edit_role" name="role" required>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="staff">Staff</option>
      </select>
    </div>
    <div>
      <label for="edit_status">Status:</label>
      <select id="edit_status" name="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div>
      <button type="submit">Update User</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="users-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Username</th>
      <th>Role</th>
      <th>Status</th>
      <th>Last Login</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . ucfirst($row['role']) . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "<td>" . ($row['last_login'] ? $row['last_login'] : 'Never') . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='8'>No users found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new user
    document.getElementById('add-user-btn').addEventListener('click', function() {
      document.getElementById('add-user-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-user-form').style.display = 'none';
    });

    // Password validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
    });

    document.getElementById('editUserForm').addEventListener('submit', function(e) {
      const password = document.getElementById('edit_password').value;
      const confirmPassword = document.getElementById('edit_confirm_password').value;

      if (password || confirmPassword) {
        if (password !== confirmPassword) {
          e.preventDefault();
          alert('Passwords do not match!');
          return false;
        }
      }
    });

    // Edit user
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const userId = this.getAttribute('data-id');
        fetchUserDetails(userId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-user-form').style.display = 'none';
    });

    // Delete user
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this user?')) {
          const userId = this.getAttribute('data-id');
          deleteUser(userId);
        }
      });
    });

    // Form submissions
    document.getElementById('userForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('actions/add_user.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('User added successfully!');
            window.location.reload();
          } else {
            alert('Error adding user: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while adding the user.');
        });
    });

    document.getElementById('editUserForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('actions/edit_user.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('User updated successfully!');
            window.location.reload();
          } else {
            alert('Error updating user: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the user.');
        });
    });
  });

  function fetchUserDetails(id) {
    fetch('actions/get_user.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_full_name').value = data.full_name;
        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_username').value = data.username;
        // Password fields left blank intentionally
        document.getElementById('edit_role').value = data.role;
        document.getElementById('edit_status').value = data.status;

        document.getElementById('edit-user-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteUser(id) {
    fetch('actions/delete_user.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('User deleted successfully!');
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) {
            row.remove();
          } else {
            window.location.reload();
          }
        } else {
          alert('Error deleting user: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the user.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>