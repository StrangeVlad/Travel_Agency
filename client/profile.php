<?php
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<section class="profile-section">
  <h1>My Profile</h1>

  <div id="success-message" class="success-message" style="display: none;"></div>
  <div id="error-message" class="error-message" style="display: none;"></div>

  <div class="profile-container">
    <form id="profile-form" method="post" action="actions/update_profile.php">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
      </div>

      <div class="form-group">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password">
        <small>Required only if changing password</small>
      </div>

      <div class="form-group">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" minlength="8">
        <small>Leave blank if not changing</small>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" minlength="8">
      </div>

      <button type="submit" id="update-button">Update Profile</button>
    </form>
  </div>
</section>

<script>
  document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const updateButton = document.getElementById('update-button');

    // Check if passwords match when trying to change password
    if (newPassword !== '' && newPassword !== confirmPassword) {
      errorMessage.textContent = 'New passwords do not match.';
      errorMessage.style.display = 'block';
      successMessage.style.display = 'none';
      return;
    }

    // Check if current password is provided when trying to change password
    if (newPassword !== '' && currentPassword === '') {
      errorMessage.textContent = 'Current password is required to set a new password.';
      errorMessage.style.display = 'block';
      successMessage.style.display = 'none';
      return;
    }

    // Disable button to prevent multiple submissions
    updateButton.disabled = true;
    updateButton.textContent = 'Updating...';

    // AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'actions/update_profile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
      if (this.status === 200) {
        const response = JSON.parse(this.responseText);

        if (response.success) {
          // Show success message
          successMessage.textContent = response.message;
          successMessage.style.display = 'block';
          errorMessage.style.display = 'none';

          // Clear password fields
          document.getElementById('current_password').value = '';
          document.getElementById('new_password').value = '';
          document.getElementById('confirm_password').value = '';
        } else {
          // Show error message
          errorMessage.textContent = response.message;
          errorMessage.style.display = 'block';
          successMessage.style.display = 'none';
        }

        updateButton.disabled = false;
        updateButton.textContent = 'Update Profile';
      } else {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
        updateButton.disabled = false;
        updateButton.textContent = 'Update Profile';
      }
    };

    xhr.send('name=' + encodeURIComponent(name) +
      '&email=' + encodeURIComponent(email) +
      '&phone=' + encodeURIComponent(phone) +
      '&current_password=' + encodeURIComponent(currentPassword) +
      '&new_password=' + encodeURIComponent(newPassword));
  });
</script>

<?php include 'includes/footer.php'; ?>