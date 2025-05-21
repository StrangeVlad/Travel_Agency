<?php
session_start();
$isAdmin = isset($_SESSION['admin']);

// Database connection setup
$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Fetching all contact request data from the database
$stmt = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #fff);
            padding: 40px;
            text-align: center;
            direction: rtl;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn-action {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            margin: 5px;
        }

        .btn-download {
            background-color: #3498db;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-view {
            background-color: #2ecc71;
        }

        /* Aligning ID column to the right */
        td:first-child,
        th:first-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <h2>📋 All Requests</h2>
    <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    <table>
        <thead>
            <tr>
                <th>Actions</th>
                <th>Delivery Date</th>
                <th>Expiration Date</th>
                <th>Date</th>
                <th>Destination</th>
                <th>Email</th>
                <th>Name</th>
                <th>ID</th> <!-- ID column moved to the last -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td>
                        <form action="view_request.php" method="get">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                            <button class="btn-action btn-view" type="submit">View</button>
                        </form>
                        <form action="delete_request.php" method="post" onsubmit="return confirm('Are you sure you want to delete this request?')">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                            <button class="btn-action btn-delete" type="submit">Delete</button>
                        </form>
                    </td>
                    <td><?= htmlspecialchars($request['delivery_date']) ?></td>
                    <td><?= htmlspecialchars($request['expiration_date']) ?></td>
                    <td><?= htmlspecialchars($request['created_at']) ?></td>
                    <td><?= htmlspecialchars($request['destination']) ?></td>
                    <td><?= htmlspecialchars($request['email']) ?></td>
                    <td><?= htmlspecialchars($request['name']) ?></td>
                    <td><?= htmlspecialchars($request['id']) ?></td> <!-- ID column moved to the last -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>