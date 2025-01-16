<?php
// Include the database connection file
include 'db.php';

// Start the session to get the worker_id (assuming worker_id is stored in the session)
session_start();
if (!isset($_SESSION['worker_id'])) {
    die("Worker not logged in.");
}
$worker_id = $_SESSION['worker_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    // Update worker status in the database
    $sql = "UPDATE workers SET status = ? WHERE worker_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $status, PDO::PARAM_STR);
    $stmt->bindParam(2, $worker_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating status: " . $stmt->errorInfo()[2] . "');</script>";
    }
    $stmt->closeCursor();
}

// Fetch worker status from the database
$sql = "SELECT status FROM workers WHERE worker_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $worker_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$status = "active"; // Default status
if ($result) {
    $status = $result['status'];
}
$stmt->closeCursor();

// Fetch job requests assigned to the worker
$jobs_query = $pdo->prepare("
    SELECT 
        users.name AS user_name,
        users.address AS user_address,
        bookings.booking_date
    FROM 
        bookings
    JOIN 
        users ON bookings.user_id = users.user_id
    WHERE 
        bookings.worker_id = ?
");
$jobs_query->bindParam(1, $worker_id, PDO::PARAM_INT);
$jobs_query->execute();
$jobs_result = $jobs_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch feedback given to the worker
$feedback_query = $pdo->prepare("SELECT rating, review_text FROM reviews WHERE worker_id = ?");
$feedback_query->bindParam(1, $worker_id, PDO::PARAM_INT);
$feedback_query->execute();
$feedback_result = $feedback_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            /*background-image: url(image/image4.webp);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;*/
        }
        .navbar {
            background-color: rgb(108, 105, 180);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        #main-content {
            padding: 20px;
            background-color: rgb(208, 224, 239);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="worker_dashboard.php">Local-Hand</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="job_request.php">Job Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Previous Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="worker_dashboard.php?section=feedbacks">Feedbacks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="worker_profile.php">Manage Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Salary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Help Support</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="" class="form-inline">
                            <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                                <option value="active" <?php echo ($status === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </form>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="main-content">
        <h2>Dashboard</h2>

        <?php
            // Determine which section to display
            $section = $_GET['section'] ?? 'job_requests';

            if ($section === 'job_requests'): ?>
                <!-- Job Requests Section -->
                <div class="card">
                    <h3>Job Requests</h3>
                    <?php if (count($jobs_result) > 0): ?>
                        <ul>
                            <?php foreach ($jobs_result as $job): ?>
                                <li>
                                    <strong>Client:</strong> <?php echo htmlspecialchars($job['user_name']); ?>, 
                                    <strong>Date:</strong> <?php echo htmlspecialchars($job['booking_date']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No job requests found.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($section === 'feedbacks'): ?>
                <!-- Feedback Section -->
                <div class="card">
                    <h3>Feedbacks</h3>
                    <?php if (count($feedback_result) > 0): ?>
                        <ul>
                            <?php foreach ($feedback_result as $feedback): ?>
                                <li>
                                    <strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?>, 
                                    <strong>Comment:</strong> <?php echo htmlspecialchars($feedback['review_text']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No feedbacks available.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
