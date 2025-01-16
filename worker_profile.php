<?php
// Include the database connection file
include 'db.php';

// Start the session to get the worker_id (assuming worker_id is stored in the session)
session_start();
if (!isset($_SESSION['worker_id'])) {
    die("Worker not logged in.");
}
$worker_id = $_SESSION['worker_id'];

// Get the current section, default to 'profile' if not provided
$section = $_GET['section'] ?? 'profile';

// Fetch data for the current worker
$worker = $pdo->prepare("SELECT * FROM workers WHERE worker_id = ?");
$worker->execute([$worker_id]);
$worker_data = $worker->fetch(PDO::FETCH_ASSOC);

// Fetch portfolio data
$portfolio = $pdo->prepare("SELECT * FROM portfolio WHERE worker_id = ?");
$portfolio->execute([$worker_id]);
$portfolio_data = $portfolio->fetchAll(PDO::FETCH_ASSOC);

// Fetch work experience data
$experience = $pdo->prepare("SELECT * FROM work_experience WHERE worker_id = ?");
$experience->execute([$worker_id]);
$experience_data = $experience->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions based on sections
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($section === 'info') {
        // Handle info form submission
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $address = $_POST['address'] ?? '';
        $specialty = $_POST['specialty'] ?? '';
        $experience_years = $_POST['experience_years'] ?? 0;
        $hourly_rate = $_POST['hourly_rate'] ?? 0.0;

        $sql = "UPDATE workers SET name = ?, phone = ?, email = ?, address = ?, specialty = ?, experience_years = ?, hourly_rate = ? WHERE worker_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $phone, $email, $address, $specialty, $experience_years, $hourly_rate, $worker_id]);
    } elseif ($section === 'portfolio') {
        // Handle portfolio upload
        $description = $_POST['description'] ?? '';
        $photo_url = $_FILES['photo_url']['name'] ?? '';

        if (isset($_FILES['photo_url']) && $_FILES['photo_url']['error'] === UPLOAD_ERR_OK) {
            $target_dir = __DIR__ . "/uploads/"; // Set the correct uploads directory
            $target_file = $target_dir . basename($_FILES['photo_url']['name']);

            // Check if the uploads/ directory exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
            }

            if (move_uploaded_file($_FILES['photo_url']['tmp_name'], $target_file)) {
                // File upload successful, insert into the database
                $sql = "INSERT INTO portfolio (worker_id, photo_url, description) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$worker_id, "uploads/" . basename($_FILES['photo_url']['name']), $description]);
            } else {
                echo "<p style='color:red;'>Failed to move uploaded file.</p>";
            }
        } else {
            echo "<p style='color:red;'>File upload error: " . $_FILES['photo_url']['error'] . "</p>";
        }
    } elseif ($section === 'experience') {
        // Handle experience form submission
        $company_name = $_POST['company_name'] ?? '';
        $position = $_POST['position'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';

        $sql = "INSERT INTO work_experience (worker_id, company_name, position, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$worker_id, $company_name, $position, $start_date, $end_date, $description]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(196, 201, 236);
        }
        .sidebar {
            background-color:rgb(66, 95, 114);
            padding: 15px;
            min-height: 100vh;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color:rgb(125, 161, 198);
        }
        .content {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="email"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            height: 100px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 sidebar">
            <h3 class="text-white">Worker Profile</h3>
            <a href="worker_dashboard.php">Home</a> 
            <a href="#">Dashboard</a> <!-- New Dashboard Link -->
            <a href="worker_profile.php?section=profile">Profile</a> <!-- Profile Link -->
            <a href="worker_profile.php?section=info">Info</a> <!-- Info Link -->
            <a href="worker_profile.php?section=portfolio">Portfolio</a> <!-- Portfolio Link -->
            <a href="worker_profile.php?section=experience">Experience</a> <!-- Experience Link -->
        </div>
        <div class="col-md-9 content">
    <!-- Display Profile Photo and Name -->
    <div>
        <?php 
        // Check if the worker has a photo in the portfolio table
        $portfolio_item = $pdo->prepare("SELECT * FROM portfolio WHERE worker_id = ? LIMIT 1");
        $portfolio_item->execute([$worker_id]);
        $portfolio_data = $portfolio_item->fetch(PDO::FETCH_ASSOC);

        // Display photo and name
        if (!empty($portfolio_data['photo_url'])): ?>
            <img src="<?= htmlspecialchars($portfolio_data['photo_url']) ?>" alt="Profile Photo" class="profile-photo" style="width: 100px; height: 100px; object-fit: cover;">
        <?php else: ?>
            <img src="default-profile.png" alt="Profile Photo" class="profile-photo" style="width: 100px; height: 100px; object-fit: cover;"> <!-- Default photo if not set -->
        <?php endif; ?>
        <h3><?= htmlspecialchars($worker_data['name']) ?></h3>
        <h4><?= htmlspecialchars($worker_data['email']) ?></h4>
        <h4><?= htmlspecialchars($worker_data['specialty']) ?></h4>
    </div>



            <?php if ($section === 'info'): ?>
                <h2>Update Info</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($worker_data['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($worker_data['phone']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($worker_data['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <textarea name="address"><?= htmlspecialchars($worker_data['address']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Specialty:</label>
                        <input type="text" name="specialty" value="<?= htmlspecialchars($worker_data['specialty']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Experience (Years):</label>
                        <input type="number" name="experience_years" value="<?= htmlspecialchars($worker_data['experience_years']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Hourly Rate:</label>
                        <input type="number" name="hourly_rate" step="0.01" value="<?= htmlspecialchars($worker_data['hourly_rate']) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            <?php elseif ($section === 'portfolio'): ?>
                <h2>Portfolio</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Photo:</label>
                        <input type="file" name="photo_url" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Portfolio</button>
                </form>
                <h3>Existing Portfolio</h3>
                <?php foreach ($portfolio_data as $item): ?>
                    <div>
                        <img src="<?= htmlspecialchars($item['photo_url']) ?>" alt="Portfolio Image" style="width: 100px;">
                        <p><?= htmlspecialchars($item['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($section === 'experience'): ?>
                <h2>Work Experience</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Company Name:</label>
                        <input type="text" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label>Position:</label>
                        <input type="text" name="position" required>
                    </div>
                    <div class="form-group">
                        <label>Start Date:</label>
                        <input type="date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date:</label>
                        <input type="date" name="end_date">
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Experience</button>
                </form>
                <h3>Existing Experience</h3>
                <?php foreach ($experience_data as $exp): ?>
                    <div>
                        <p><strong>Company:</strong> <?= htmlspecialchars($exp['company_name']) ?></p>
                        <p><strong>Position:</strong> <?= htmlspecialchars($exp['position']) ?></p>
                        <p><strong>Duration:</strong> <?= htmlspecialchars($exp['start_date']) ?> - <?= htmlspecialchars($exp['end_date']) ?></p>
                        <p><?= htmlspecialchars($exp['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
