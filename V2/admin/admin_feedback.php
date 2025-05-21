<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "agence_voyage");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback with user info
$sql = "SELECT f.*, u.first_name, u.last_name
        FROM feedbacks f
        LEFT JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);

// Optionally, mark feedback as viewed
$conn->query("UPDATE feedbacks SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Feedback</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1976d2;
        }

        .feedback-card {
            border-bottom: 1px solid #ddd;
            padding: 15px;
        }

        .feedback-card:last-child {
            border-bottom: none;
        }

        .user-name {
            font-weight: bold;
            color: #333;
        }

        .message {
            margin: 10px 0;
            color: #555;
        }

        .rating {
            color: gold;
            margin-bottom: 5px;
        }

        .date {
            font-size: 12px;
            color: #888;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #1976d2;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📬 User Feedback</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="feedback-card">
                <div class="user-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?= $i <= (int)$row['rating'] ? '⭐' : '☆' ?>
                    <?php endfor; ?>
                </div>
                <div class="message"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                <div class="date">🕒 <?= date("d M Y - H:i", strtotime($row['created_at'])) ?></div>
                <a href="delete_feedback.php?id=<?= $row['feedback_id'] ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this feedback?');">🗑️ Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No feedback available.</p>
    <?php endif; ?>

    <a class="back-link" href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
