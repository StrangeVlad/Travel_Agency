<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب بيانات المستخدمين مع عمود is_blocked
$result = $conn->query("SELECT id, first_name, last_name, email, created_at, is_blocked FROM users");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <style>
        body {
            font-family: Tahoma;
            padding: 30px;
            background: #f9f9f9;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #00796b;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .btn {
            padding: 6px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .block { background-color: #f44336; }
        .unblock { background-color: #4caf50; }
        .edit { background-color: #2196f3; }
        .delete { background-color: #9e9e9e; }
    </style>
</head>
<body>
    <h1>👥 User Management</h1>
    <a href="dashboard.php">⬅ Back to Dashboard</a><br><br>
    <table>
        <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
            <th>Registered At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
        <td><?= htmlspecialchars($row["first_name"] ?? '') ?></td>
        <td><?= htmlspecialchars($row["last_name"] ?? '') ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= $row["created_at"] ?></td>
            <td><?= $row["is_blocked"] ? "🚫 Blocked" : "✅ Active" ?></td>
            <td>
                <?php if ($row["is_blocked"]): ?>
                    <a href="toggle_block.php?id=<?= $row['id'] ?>&action=unblock" class="btn unblock">Unblock</a>
                <?php else: ?>
                    <a href="toggle_block.php?id=<?= $row['id'] ?>&action=block" class="btn block">Block</a>
                <?php endif; ?>
                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn delete">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
