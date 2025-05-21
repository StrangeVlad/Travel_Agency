<?php
$host = 'localhost';
$db = 'agence_voyage';
$user = 'root';
$pass = '';

$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$results = [];

if (isset($_GET['search_type'])) {
    if ($_GET['search_type'] === 'client' && isset($_GET['query']) && !empty(trim($_GET['query']))) {
        $query = trim($_GET['query']);
        $stmt = $conn->prepare("SELECT * FROM users WHERE first_name LIKE :q OR last_name LIKE :q OR email LIKE :q ");
        $stmt->execute(['q' => "%$query%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($_GET['search_type'] === 'month' && isset($_GET['month']) && !empty($_GET['month'])) {
        $month = $_GET['month'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
        $stmt->execute(['month' => $month]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Clients</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #1976d2;
        }

        .search-boxes {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            display: inline-block;
            margin: 10px;
        }

        input, select, button {
            padding: 10px 15px;
            margin: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #1976d2;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0d47a1;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #1976d2;
            color: white;
        }

        tr:hover {
            background-color: #f0f8ff;
        }

        .no-results {
            text-align: center;
            margin-top: 30px;
            color: #888;
        }
    </style>
</head>
<body>



<?php if (!empty($results)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Birthday</th>
            <th>Registered At</th>
        </tr>
        <?php foreach ($results as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
            <td><?= htmlspecialchars($user['last_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['birthday']) ?></td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php elseif (isset($_GET['search_type'])): ?>
    <div class="no-results">No results found.</div>
<?php endif; ?>

</body>
</html>
