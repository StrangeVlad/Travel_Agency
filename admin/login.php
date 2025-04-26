<?php
session_start();
include 'includes/db_connection.php';
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
    if (password_verify($password, $row['password'])) {
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
</head>

<body>
  <h1>Admin Login</h1>

  <?php if (isset($error)) {
    echo "<p>$error</p>";
  } ?>

  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div>
      <button type="submit">Login</button>
    </div>
  </form>
</body>

</html>