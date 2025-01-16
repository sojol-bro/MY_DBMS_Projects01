<?php
include 'db.php';
session_start();
/*if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}*/

// Fetch all reviews along with the names of the users and workers
$query = "
    SELECT r.review_id, r.review_text, r.rating, u.name AS user_name, w.name AS worker_name
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN workers w ON r.worker_id = w.worker_id
    ORDER BY r.created_at DESC
";
$reviews = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management</title>
    <style>
        body {
            background-color: rgb(245, 245, 245);
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .navbar {
            background-color: #333;
            color: white;
            position: sticky;
            top: 0;
            width: 100%;
            padding: 10px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #fff;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_dashboard.php">Home</a>
        <a href="worker_approval.php">Worker Approval</a>
        <a href="review_handle.php">Review Management</a>
    </div>

    <h1>Review Management</h1>

    <table>
        <tr>
            <th>Review ID</th>
            <th>Review Giver</th>
            <th>Worker</th>
            <th>Rating</th>
            <th>Review</th>
        </tr>
        <?php foreach ($reviews as $review): ?>
            <tr>
               <td><?= htmlspecialchars($review['review_id']) ?></td>
                <td><?= htmlspecialchars($review['user_name']) ?></td>
                <td><?= htmlspecialchars($review['worker_name']) ?></td>
                <td><?= htmlspecialchars($review['rating']) ?></td>
                <td><?= htmlspecialchars($review['review_text']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
