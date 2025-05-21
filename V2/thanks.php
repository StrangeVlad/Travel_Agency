<?php
$name = $_GET['name'] ?? 'Guest';
$email = $_GET['email'] ?? 'Not provided';
$destination = $_GET['destination'] ?? 'غير محددة';
$delivery_date = $_GET['delivery_date'] ?? 'N/A';
$expiration_date = $_GET['expiration_date'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #fff);
            padding: 40px;
            text-align: center;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin: auto;
            width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .card h1 {
            color: #00796b;
        }
        .info {
            margin: 20px 0;
            font-size: 18px;
        }
        .btn-download {
            background: rgb(171, 182, 228);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #00796b;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .seal {
            width: 80px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>🎉 Thank You, <?= htmlspecialchars($name) ?>!</h1>
    
    <form action="generate_pdf.php" method="POST">
        <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="destination" value="<?= htmlspecialchars($destination) ?>">
        <input type="hidden" name="delivery_date" value="<?= htmlspecialchars($delivery_date) ?>">
        <input type="hidden" name="expiration_date" value="<?= htmlspecialchars($expiration_date) ?>">
        <button class="btn-download" type="submit">📄 Download Confirmation PDF</button>
    </form>
    <a class="back-link" href="agence.html">← Back to Home</a>
    
    <img class="seal" src="Photo/kach2.png" alt="Official Seal">
</div>

</body>
</html>
