<?php
session_start();


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agence_voyage";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in all fields";
    } else {
        // Query database for user
        $sql = "SELECT id, email, password, first_name, last_name FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password (using password_verify if passwords are hashed)
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_regenerate_id();

                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                header("Location: destination.php");
                // Redirect to referring page if exists, otherwise to home
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']); // Clear the stored URL
                    header("Location: $redirect");
                    exit();
                } else {
                    header("Location: destination.php");
                    exit();
                }
            } else {
                $error_message = "Invalid email or password";
            }
        } else {
            $error_message = "Invalid email or password";
        }
    }
}

// Store referring page if available
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = urldecode($_GET['redirect']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agence De Voyage</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
        }

        .logo img {
            height: 70px;
            margin: 10px 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .navbar {
            background-color: #fff;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .nav-links {
            list-style: none;
            padding: 0;
        }

        .nav-links li {
            display: inline;
            margin: 0 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .register-form {
            padding: 40px 0;
        }

        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        footer.footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <img src="Photo/logo.png.jpg" alt="Logo">
        </div>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="agence.html">Home</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </nav>
    </header>

    <section class="register-form">
        <div class="container">
            <h1>Login to Your Account</h1>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Your Email Address" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Your Password" required>

                <button type="submit">Login</button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.html">Register here</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SkyLine. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>

<?php $conn->close(); ?>