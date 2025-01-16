<?php
include 'db.php';
session_start();
/*if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}*/

// Fetch pending worker registrations
$pending_workers = $pdo->query("SELECT * FROM workers WHERE approved IS NULL")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Approval</title>
    <style>
        body {
            background-color: rgb(136, 192, 245);
            font-family: Arial, sans-serif;
            padding: 20px;
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
            background-color: rgb(146, 172, 192);
            color: white;
        }

        td {
            background-color: rgb(178, 211, 235);;
        }

        a {
            color: blue;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Worker Approval</h1>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Specialty</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($pending_workers as $worker): ?>
            <tr>
                <td><a href="worker_profile_view.php?worker_id=<?= $worker['worker_id'] ?>"><?= $worker['name'] ?></a></td>
                <td><?= $worker['email'] ?></td>
                <td><?= $worker['specialty'] ?></td>
                <td>
                    <form method="POST" action="admin_actions.php" style="display: inline;">
                        <input type="hidden" name="action" value="approve_worker">
                        <input type="hidden" name="worker_id" value="<?= $worker['worker_id'] ?>">
                        <button type="submit">Approve</button>
                    </form>
                    <form method="POST" action="admin_actions.php" style="display: inline;">
                        <input type="hidden" name="action" value="reject_worker">
                        <input type="hidden" name="worker_id" value="<?= $worker['worker_id'] ?>">
                        <button type="submit">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
