<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to book a worker.";
    exit;
}

$message = ""; // Variable to hold success or error messages

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['worker_id'])) {
    $worker_id = $_POST['worker_id'];
    $Description = $_POST['Description'];
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d'); // Current date for booking
    $status = "Pending"; // Default status

    // Ensure user_id exists in the users table before booking
    $check_user_query = "SELECT user_id FROM users WHERE user_id = :user_id";
    $check_stmt = $pdo->prepare($check_user_query);
    $check_stmt->execute([':user_id' => $user_id]);

    if ($check_stmt->rowCount() > 0) {
        // Insert the booking into the database using PDO
        $query = "INSERT INTO bookings (user_id, worker_id, booking_date, status, Description) 
                  VALUES (:user_id, :worker_id, :booking_date, :status, :Description)";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([
            ':user_id' => $user_id,
            ':worker_id' => $worker_id,
            ':booking_date' => $date,
            ':status' => $status,
            ':Description' => $Description
        ])) {
            $message = "Booking confirmed!";
        } else {
            $message = "Error: " . implode(" ", $stmt->errorInfo());
        }
    } else {
        $message = "Error: Invalid user ID.";
    }
}

// Fetch distinct job types from the database using PDO
$job_types_query = "SELECT DISTINCT specialty FROM workers";
$job_types_result = $pdo->query($job_types_query);

// Handle the location filter after a job type is selected
$workers_result = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_type'])) {
    $job_type = $_POST['job_type'];
    $address = $_POST['address'];

    // Using prepared statements with PDO for security
    $workers_query = "SELECT worker_id, name, specialty, address,hourly_rate 
                      FROM workers 
                      WHERE specialty = :job_type AND address LIKE :address";
    $stmt = $pdo->prepare($workers_query);
    $stmt->execute([
        ':job_type' => $job_type,
        ':address' => "%$address%"
    ]);
    $workers_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Worker</title>
    <style>
        body {
            background-color: rgb(167, 179, 244);
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .sidebar {
            width: 250px;
            background: rgb(108, 105, 180);
            color: white;
            padding-top: 20px;
            height: 100vh;
        }
        .sidebar h1 {
            text-align: center;
            padding: 10px;
            font-size: 24px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: left;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: block;
        }
        .sidebar ul li:hover {
            background: #34495e;
        }
        .booking-panel {
            margin: 20px auto;
            padding: 20px;
            background-color:rgb(162, 147, 230);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
        }
        .booking-panel h2 {
            font-size: 28px;
            font-weight: bold;
        }
        select, input[type="text"], textarea, button {
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }
        button {
            background-color: white;
            color: rgb(0, 0, 128);
            cursor: pointer;
        }
        button:hover {
            background-color: lightgray;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Local-Hand</h1>
        <ul>
             <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="book.php">Bookings</a></li>
            <li><a href="reviews.php">Ratings & Reviews</a></li>
            <li><a href="user_profile.php">Profile</a></li>
            <li><a href="#">Help & Support</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="booking-panel">
        <form method="POST">
            <h2>Select Job Type</h2>
            <select name="job_type" required>
                <option value="" disabled selected>Select a job type</option>
                <?php
                if ($job_types_result && $job_types_result->rowCount() > 0) {
                    while ($job = $job_types_result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($job['specialty']) . "'>" . htmlspecialchars($job['specialty']) . "</option>";
                    }
                }
                ?>
            </select>
            <input type="text" name="address" placeholder="Enter location (optional)">
            <button type="submit">Search</button>
        </form>
        <?php if ($workers_result && count($workers_result) > 0): ?>
                <h3>Available Workers:</h3>
                <?php foreach ($workers_result as $worker): ?>
                    <p><?php echo htmlspecialchars($worker['name']); ?> - <?php echo htmlspecialchars($worker['specialty']); ?>-rate:<?php echo htmlspecialchars($worker['hourly_rate']); ?></p>
                    <form method="POST">
                        <input type="hidden" name="worker_id" value="<?php echo $worker['worker_id']; ?>">
                        <textarea name="Description" placeholder="Describe the task" required></textarea>
                        <button type="submit">Book This Worker</button>
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php if ($message): ?>
            <p class="message"> <?php echo htmlspecialchars($message); ?> </p>
        <?php endif; ?>
    </div>
</body>
</html>
