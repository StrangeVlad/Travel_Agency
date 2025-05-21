<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(" Database connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("
    SELECT r.*, u.first_name, u.last_name, u.email AS user_email, u.is_blocked, u.id AS user_id 
    FROM travel_requests r 
    LEFT JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Travel Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 60px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color:rgb(241, 241, 241);
        }
        button {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            margin: 2px 0;
            cursor: pointer;
            font-weight: bold;
            color: black;
        }
        .approve { background-color: #2ecc71; }
        .reject { background-color: #e67e22; }
        .delete { background-color: #e74c3c; }
        .block { background-color: #c0392b; }
        .unblock { background-color: #27ae60; }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 10px;
        }
    .modal-content form button,
    .modal-content > div > button {
        padding: 5px 60px;
        font-size: 20px;
    }
        
    </style>
</head>
<body>
<h2>📋 All Travel Package Requests</h2>

<a href="dashboard.php">⬅ Back to Dashboard</a>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Email</th>
        <th>Destination</th>
        <th>Dates</th>
        <th>Hotel</th>
        <th>User Status</th>
        <th>Request Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $req): ?>
        <tr>
            <td><?= htmlspecialchars($req['id']) ?></td>
            <td><?= htmlspecialchars($req['fullname'] )?></td>
            <td><?= htmlspecialchars($req['email']) ?></td>
            <td><?= htmlspecialchars($req['destination']) ?></td>
            <td><?= htmlspecialchars($req['depart_date']) ?> to <?= htmlspecialchars($req['return_date']) ?></td>
            <td><?= htmlspecialchars($req['hotel']) ?></td>
            <td><?= $req['is_blocked'] ? '🔐 Blocked' : '✅ Active' ?></td>
            <td>
                <?php if (!empty($req['admin_status'])): ?>
                    <span style="font-weight:bold; color:<?= $req['admin_status'] === 'accept' ? 'green' : 'red' ?>">
                        <?= $req['admin_status'] === 'accept' ? '✅ Accepted' : '❌ Refused' ?>
                    </span>
                <?php else: ?>
                    <span style="color: gray;">⏳ Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <button onclick="document.getElementById('details-modal-<?= $req['id'] ?>').style.display='block'">View Details</button>

                <div id="details-modal-<?= $req['id'] ?>" class="modal">
                    <div class="modal-content">
                        <h2>Travel Request Details  </h2>
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($req['fullname']) ?></p>
                        <p><strong>Passport:</strong> <?= htmlspecialchars($req['passport']) ?></p>
                        <p><strong>Passport Expiry:</strong> <?= htmlspecialchars($req['passport_expire']) ?></p>
                        <p><strong>Birthdate:</strong> <?= htmlspecialchars($req['birthdate']) ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($req['address']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($req['phone']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($req['email']) ?></p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($req['gender']) ?></p>
                        <hr>
                        <p><strong>Destination:</strong> <?= htmlspecialchars($req['destination']) ?></p>
                        <p><strong>Depart Date:</strong> <?= htmlspecialchars($req['depart_date']) ?></p>
                        <p><strong>Return Date:</strong> <?= htmlspecialchars($req['return_date']) ?></p>
                        <p><strong>Activities:</strong> <?= htmlspecialchars($req['activities']) ?></p>
                        <p><strong>Transport:</strong> <?= htmlspecialchars($req['transport']) ?></p>
                        <p><strong>Flight Class:</strong> <?= htmlspecialchars($req['flight_class']) ?></p>
                        <p><strong>Hotel:</strong> <?= htmlspecialchars($req['hotel']) ?></p>
                        <p><strong>Room Type:</strong> <?= htmlspecialchars($req['room_type']) ?></p>
                        <p><strong>Meal:</strong> <?= htmlspecialchars($req['meal']) ?></p>
                        <p><strong>Visa Required:</strong> <?= htmlspecialchars($req['visa']) ?></p>
                        <p><strong>Guide Needed:</strong> <?= htmlspecialchars($req['guide']) ?></p>
                        <p><strong>Adults:</strong> <?= htmlspecialchars($req['adult']) ?></p>
                        <p><strong>Children:</strong> <?= htmlspecialchars($req['child']) ?></p>
                        <p><strong>Request Created:</strong> <?= htmlspecialchars($req['created_at']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($req['status']) ?></p>
                        <p><strong>Admin Status:</strong> <?= htmlspecialchars($req['admin_status']) ?></p>
                        <?php if (empty($req['admin_status'])): ?>
                            <form method="POST" action="user_request.php">
                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                <br>
                                <button name="action" value="accept" class="approve">✅ Accept</button>
                                <button name="action" value="refuse" class="reject">❌ Refuse</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="admin_reset_status.php">
                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                <button name="action" value="reset" class="delete">♻ Reset</button>
                         </div>
                            </form>
                        <?php endif; ?>
                           <button onclick="document.getElementById('details-modal-<?= $req['id'] ?>').style.display='none'">Close</button>
                         </div>    
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>