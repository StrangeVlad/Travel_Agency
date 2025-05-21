<?php
session_start();
$isAdmin = isset($_SESSION['admin']);

// Database connection
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

$request_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($request_id) {
    $stmt = $pdo->prepare("SELECT * FROM contact_requests WHERE id = :id");
    $stmt->execute(['id' => $request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        die("❌ Request not found.");
    }

    // ❌ Auto-refuse if unpaid and 2+ days passed
    $createdAt = new DateTime($request['created_at']);
    $now = new DateTime();
    $interval = $createdAt->diff($now);

    if (
        $interval->days >= 2 &&
        !$request['is_paid'] && 
        empty($request['admin_status'])
    ) {
        $stmt = $pdo->prepare("UPDATE contact_requests SET admin_status = 'refused' WHERE id = :id");
        $stmt->execute(['id' => $request_id]);
        $request['admin_status'] = 'refused'; // Reflect change
    }

} else {
    die("❌ Invalid request ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Details</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f5f9;
            padding: 40px;
            text-align: center;
        }
        .request-details {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
        }
        .request-details p {
            font-size: 18px;
            margin: 10px 0;
        }
        .btn-back {
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 30px;
        }
        .btn-back:hover {
            background-color: #0d47a1;
        }
        .status-box {
            margin-top: 25px;
            padding: 20px;
            border-left: 6px solid;
            border-radius: 8px;
        }
        .accepted {
            background-color: #e8f5e9;
            border-color: #2e7d32;
            color: #2e7d32;
        }
        .refused {
            background-color: #ffebee;
            border-color: #c62828;
            color: #c62828;
        }
        .admin-form {
            margin-top: 30px;
            text-align: left;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .form-buttons {
            margin-top: 15px;
        }
        .form-buttons button {
            padding: 10px 15px;
            margin-right: 10px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .accept-btn {
            background-color: #27ae60;
        }
        .refuse-btn {
            background-color: #c0392b;
        }  
    </style>
</head>
<body>

<h2>📋 Request Details</h2>

<div class="request-details">
    <p><strong>Name:</strong> <?= htmlspecialchars($request['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($request['email']) ?></p>
    <p><strong>Destination:</strong> <?= htmlspecialchars($request['destination']) ?></p>
    <p><strong>Requested Date:</strong> <?= htmlspecialchars($request['created_at']) ?></p>
    <p><strong>Delivery Date:</strong> <?= htmlspecialchars($request['delivery_date']) ?></p>
    <p><strong>Expiration Date:</strong> <?= htmlspecialchars($request['expiration_date']) ?></p>
    <p><strong>Paid:</strong> <?= $request['is_paid'] ? '✅ Yes' : '❌ No' ?></p>

    <?php if (!empty($request['admin_status'])): ?>
        <div class="status-box <?= $request['admin_status'] === 'accept' ? 'accepted' : 'refused' ?>">
            <h3><?= $request['admin_status'] === 'accept' ? '✅ Accepted' : '❌ Refused' ?></h3>
        </div>
    <?php endif; ?>
</div>

<?php if ($isAdmin && empty($request['admin_status'])): ?>
    <form action="handle_request_decision.php" method="POST" class="admin-form">
        <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['id']) ?>">
        <div class="form-buttons">
            <button type="submit" name="action" value="accept" class="accept-btn">✅ Accept</button>
            <button type="submit" name="action" value="refuse" class="refuse-btn">❌ Refuse</button>
        </div>
    </form>
<?php endif; ?>

<a href="voyage_org.php" class="btn-back">⬅️ Back to All Requests</a>

</body>
</html>
