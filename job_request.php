<?php
// Include database connection
include 'db.php';

// Start session to get worker ID (assuming worker_id is stored in session)
session_start();
if (!isset($_SESSION['worker_id'])) {
    die("Worker not logged in.");
}
$worker_id = $_SESSION['worker_id'];

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch job requests for the current worker
$sql = "SELECT b.booking_id, b.status, b.booking_date, u.name, u.email
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        WHERE b.worker_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$worker_id]);
$job_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle accept or reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token.");
    }

    if (in_array($action, ['accept', 'reject']) && $booking_id) {
        $new_status = $action === 'accept' ? 'Accepted' : 'Rejected';
        $update_sql = "UPDATE bookings SET status = ? WHERE booking_id = ? AND worker_id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        
        if ($update_stmt->execute([$new_status, $booking_id, $worker_id])) {
            // Optionally, set a success message in session
            $_SESSION['message'] = "Job request $new_status successfully.";
        } else {
            // Optionally, set an error message in session
            $_SESSION['error'] = "Failed to update job request.";
        }

        header("Location: job_request.php"); // Redirect to refresh the page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(182, 222, 236);
        }
        .container {
            margin-top: 30px;
        }
        .job-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .job-card .status {
            font-weight: bold;
        }
        .job-card.accepted {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .job-card.rejected {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .job-card.pending {
            background-color:rgb(241, 229, 191);
            border-color:rgb(236, 224, 187);
        }
        .btn {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Job Requests</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($job_requests)): ?>
        <p>No job requests at the moment.</p>
    <?php else: ?>
        <?php foreach ($job_requests as $request): ?>
            <div class="job-card <?= strtolower($request['status']) ?>">
                <p><strong>Name:</strong> <?= htmlspecialchars($request['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($request['email']) ?></p
                <p><strong>Requested At:</strong> <?= htmlspecialchars($request['booking_date']) ?></p>
                <p class="status"><strong>Status:</strong> <?= htmlspecialchars($request['status']) ?></p>

                <?php if ($request['status'] === 'Pending'): ?>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="booking_id" value="<?= $request['booking_id'] ?>">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="btn btn-success">Accept</button>
                    </form>

                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="booking_id" value="<?= $request['booking_id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>