<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']); // تأمين من إدخال ضار

    // استخدم prepared statement للحذف
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_bookings.php"); // إعادة تحميل الصفحة بعد الحذف
    exit();
}

$query = "
    SELECT 
        r.id,
        u.first_name,
        u.last_name,
        u.email,
        d.country_name AS destination,
        h.name AS hotel,
        r.created_at
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN destinations d ON r.destination_id = d.destination_id
    JOIN hotels h ON r.hotel_id = h.hotel_id
    ORDER BY r.created_at DESC
";


$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f9f9f9;
        }

        h1 {
            color: #333;
            font-size: 28px;
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f1f1f1;
        }

        tr:hover {
            background-color: #fafafa;
        }

        .delete-link {
            color: red;
            text-decoration: none;
        }

        .delete-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Manage Bookings</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 30px;
                background-color: #f9f9f9;
            }

            h1 {
                color: #333;
                font-size: 28px;
            }

            a.back-link {
                display: inline-block;
                margin-bottom: 20px;
                text-decoration: none;
                color: #3498db;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background-color: #fff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            th,
            td {
                padding: 12px 16px;
                text-align: left;
                border-bottom: 1px solid #eee;
            }

            th {
                background-color: #f1f1f1;
            }

            tr:hover {
                background-color: #fafafa;
            }

            .delete-link {
                color: red;
                text-decoration: none;
            }

            .delete-link:hover {
                text-decoration: underline;
            }
        </style>
    </head>

    <body>

        <h1>📋 Manage Bookings</h1>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Email</th>
                    <th>Hotel</th>
                    <th>Destination</th>
                    <th>Booking Date</th>
                    <th>🗑 Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= htmlspecialchars($row["first_name"]) ?></td>
                        <td><?= htmlspecialchars($row["last_name"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["hotel"]) ?></td>
                        <td><?= htmlspecialchars($row["destination"]) ?></td>
                        <td><?= $row["created_at"] ?></td>
                        <td>
                            <a href="?delete=<?= $row["id"] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this booking?');">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </body>

    </html>