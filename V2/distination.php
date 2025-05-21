<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$name = isset($_SESSION['name']) ? $_SESSION['name'] : $_SESSION['email']; // استخدام الاسم أو البريد الإلكتروني كبديل

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background-color: #f9f9f9;
        }

        .container {
            display: block;
            width: 50%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin: 100px auto;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #ff4d4d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>! 🎉</h1>
        <p>You have successfully logged in. Enjoy exploring your destinations! ✈️</p>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

</body>
</html>
