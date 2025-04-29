<?php
session_start();
include '../res/db_connection.php';
include 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = sanitize_input($_POST['email']);
  $password = sanitize_input($_POST['password']);

  $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password']) || $password == $row['password']) {
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['name'] = $row['name'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['role'] = $row['role'];

      header("Location: index.php");
      exit;
    } else {
      $error = "Invalid password";
    }
  } else {
    $error = "Invalid email or not an admin account";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Travel Agency</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #3b82f6;
      --primary-dark: #1d4ed8;
      --secondary: #f3f4f6;
      --text-dark: #1f2937;
      --text-light: #6b7280;
      --error: #ef4444;
      --success: #10b981;
      --white: #ffffff;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background-color: #f9fafb;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem;
    }

    .login-container {
      background-color: var(--white);
      border-radius: 12px;
      box-shadow: var(--shadow);
      width: 100%;
      max-width: 420px;
      padding: 2rem;
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-header h1 {
      color: var(--text-dark);
      font-size: 1.875rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .login-header p {
      color: var(--text-light);
      font-size: 1rem;
    }

    .login-form .form-group {
      margin-bottom: 1.5rem;
    }

    .login-form label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--text-dark);
      font-weight: 500;
      font-size: 0.938rem;
    }

    .input-group {
      position: relative;
    }

    .input-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      padding-left: 2.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.5rem;
      font-size: 1rem;
      color: var(--text-dark);
      background-color: var(--white);
      transition: all 0.3s ease;
    }

    .input-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    .input-group i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
    }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      cursor: pointer;
      background: none;
      border: none;
    }

    .login-btn {
      width: 100%;
      padding: 0.75rem;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .login-btn:hover {
      background-color: var(--primary-dark);
    }

    .error-message {
      background-color: rgba(239, 68, 68, 0.1);
      color: var(--error);
      padding: 0.75rem;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
      font-size: 0.938rem;
      display: flex;
      align-items: center;
    }

    .error-message i {
      margin-right: 0.5rem;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Admin Login</h1>
      <p>Enter your credentials to access the dashboard</p>
    </div>

    <?php if (isset($error)) { ?>
      <div class="error-message">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error; ?>
      </div>
    <?php } ?>

    <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" id="email" name="email" placeholder="admin@example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="login-btn">Sign In</button>
    </form>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  </script>
</body>

</html>