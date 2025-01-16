<?php
include 'db.php';
session_start();
$specialty_id = isset($_GET['specialty_id']) ? intval($_GET['specialty_id']) : 0;

$query = "SELECT worker_id, name, address, email, specialty, hourly_rate FROM workers WHERE specialty_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$specialty_id]);
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['user_id'])) {
        $worker_id = intval($_POST['worker_id']);
        $user_id = $_SESSION['user_id'];
        $status = 'pending';
        $booking_date = date('Y-m-d H:i:s');

        $insertQuery = "INSERT INTO bookings (user_id, worker_id, booking_date, status) VALUES (?, ?, ?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([$user_id, $worker_id, $booking_date, $status]);
        echo "<script>alert('Booking Confirmed!'); window.location.href='worker_service.php?specialty_id=$specialty_id';</script>";
    } else {
        echo "<script>alert('You must be logged in to book a worker!'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Services</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color:rgb(135, 224, 240); }
        .navbar { position: sticky; top: 0; background: rgb(33, 121, 169); padding: 10px; color: white; text-align: center; }
        .navbar a { color: white; text-decoration: none; padding: 10px; }
        .container { padding: 20px; }
        .worker-card { border: 1px solid #ddd; padding: 20px; margin: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); background: white; }
        .worker-card h3 { margin: 0 0 10px; }
        .worker-card p { margin: 5px 0; }
        .worker-card button { margin-top: 10px; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .book-btn { background-color:rgb(48, 226, 200); color: white; }
        .profile-btn { background-color:rgb(56, 139, 191); color: white; }
        .book-btn:hover, .profile-btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="user_dashboard.php">Home</a>
    </div>

    <div class="container">
        <h1>Workers with Specialty ID: <?php echo htmlspecialchars($specialty_id); ?></h1>

        <?php foreach ($workers as $worker) { ?>
            <div class="worker-card">
                <h3><?php echo htmlspecialchars($worker['name']); ?></h3>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($worker['address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($worker['email']); ?></p>
                <p><strong>Specialty:</strong> <?php echo htmlspecialchars($worker['specialty']); ?></p>
                <p><strong>Hourly Rate:</strong> <?php echo htmlspecialchars($worker['hourly_rate']); ?> USD</p>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="worker_id" value="<?php echo $worker['worker_id']; ?>">
                    <button type="submit" class="book-btn">Book</button>
                </form>
                <button class="profile-btn" onclick="window.location.href='worker_profile_view.php?worker_id=<?php echo $worker['worker_id']; ?>'">See Profile</button>
            </div>
        <?php } ?>
    </div>
</body>
</html>
