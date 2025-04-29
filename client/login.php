<?php
include 'includes/header.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}
?>

<section class="login-section">
  <h1>Login to Your Account</h1>

  <div id="error-message" class="error-message"></div>

  <form id="login-form" method="post" action="actions/login_action.php">
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" id="login-button">Login</button>
  </form>

  <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
</section>

<script>
  document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');
    const loginButton = document.getElementById('login-button');

    // Disable button to prevent multiple submissions
    loginButton.disabled = true;
    loginButton.textContent = 'Processing...';

    // AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'actions/login_action.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
      if (this.status === 200) {
        const response = JSON.parse(this.responseText);

        if (response.success) {
          // Redirect on successful login
          window.location.href = 'index.php';
        } else {
          // Display error message
          errorMessage.textContent = response.message;
          errorMessage.style.display = 'block';
          loginButton.disabled = false;
          loginButton.textContent = 'Login';
        }
      } else {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        loginButton.disabled = false;
        loginButton.textContent = 'Login';
      }
    };

    xhr.send('email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password));
  });
</script>

<?php include 'includes/footer.php'; ?>