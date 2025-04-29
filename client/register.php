<?php
include 'includes/header.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}
?>

<section class="register-section">
  <h1>Create an Account</h1>

  <div id="error-message" class="error-message" style="display:none; color:red; margin-bottom: 10px;"></div>

  <form id="register-form" method="post">
    <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required minlength="8">
    </div>
    <div class="form-group">
      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
    </div>
    <button type="submit" id="register-button">Register</button>
  </form>

  <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
</section>

<script>
  document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorMessage = document.getElementById('error-message');
    const registerButton = document.getElementById('register-button');

    // Clear any previous error
    errorMessage.style.display = 'none';
    errorMessage.textContent = '';

    // Check if passwords match
    if (password !== confirmPassword) {
      errorMessage.textContent = 'Passwords do not match.';
      errorMessage.style.display = 'block';
      return;
    }

    // Disable button to prevent multiple submissions
    registerButton.disabled = true;
    registerButton.textContent = 'Processing...';

    // AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'actions/register_action.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
      try {
        const response = JSON.parse(this.responseText);

        if (response.success) {
          window.location.href = 'login.php?registered=true';
        } else {
          errorMessage.textContent = response.message;
          errorMessage.style.display = 'block';
          registerButton.disabled = false;
          registerButton.textContent = 'Register';
        }
      } catch (e) {
        console.error('Response is not valid JSON:', this.responseText);
        errorMessage.textContent = 'Server error. Please try again later.';
        errorMessage.style.display = 'block';
        registerButton.disabled = false;
        registerButton.textContent = 'Register';
      }
    };

    xhr.onerror = function() {
      errorMessage.textContent = 'Network error. Please try again.';
      errorMessage.style.display = 'block';
      registerButton.disabled = false;
      registerButton.textContent = 'Register';
    };

    xhr.send('name=' + encodeURIComponent(name) +
      '&email=' + encodeURIComponent(email) +
      '&phone=' + encodeURIComponent(phone) +
      '&password=' + encodeURIComponent(password));
  });
</script>

<?php include 'includes/footer.php'; ?>