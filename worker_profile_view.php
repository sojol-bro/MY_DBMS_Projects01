<?php
session_start();
include 'db.php'; // Database connection

// Check if worker_id is provided in the URL
if (!isset($_GET['worker_id'])) {
    echo "Worker ID not specified.";
    exit;
}

$worker_id = intval($_GET['worker_id']);

// Fetch worker details
$workerQuery = "SELECT * FROM workers WHERE worker_id = :worker_id";
$stmt = $pdo->prepare($workerQuery);
$stmt->execute(['worker_id' => $worker_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$worker) {
    echo "Worker not found.";
    exit;
}

// Fetch work experience
$experienceQuery = "SELECT * FROM work_experience WHERE worker_id = :worker_id";
$experienceStmt = $pdo->prepare($experienceQuery);
$experienceStmt->execute(['worker_id' => $worker_id]);
$experiences = $experienceStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch worker specialty
$specialtyQuery = "SELECT specialty_name FROM specialties WHERE specialty_id = :specialty_id";
$specialtyStmt = $pdo->prepare($specialtyQuery);
$specialtyStmt->execute(['specialty_id' => $worker['specialty_id']]);
$specialty = $specialtyStmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .profile-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .profile-details h2 {
            margin: 0;
            font-size: 24px;
        }

        .profile-details p {
            margin: 5px 0;
        }

        .section {
            margin-top: 20px;
        }

        .section h3 {
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .experience {
            margin-bottom: 15px;
        }

        .experience h4 {
            margin: 0;
            font-size: 18px;
        }

        .experience p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <img class="profile-photo" src="<?= htmlspecialchars($worker['profile_photo']) ?>" 
             alt="Profile Photo" onerror="this.src='images/default-profile.jpg'">
        <div class="profile-details">
            <h2><?= htmlspecialchars($worker['name']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($worker['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($worker['phone']) ?></p>
            <p><strong>Specialty:</strong> <?= htmlspecialchars($specialty['specialty_name'] ?? 'N/A') ?></p>
            <p><strong>Hourly Rate:</strong> $<?= htmlspecialchars($worker['hourly_rate']) ?></p>
            <p><strong>Years of Experience:</strong> <?= htmlspecialchars($worker['experience_years']) ?></p>
           
        </div>
    </div>

    <!-- Bio Section -->
    <div class="section">
        <h3>Bio</h3>
        <p><?= nl2br(htmlspecialchars($worker['bio'])) ?></p>
    </div>

    <!-- Work Experience Section -->
    <div class="section">
        <h3>Work Experience</h3>
        <?php if (!empty($experiences)) : ?>
            <?php foreach ($experiences as $experience) : ?>
                <div class="experience">
                    <h4><?= htmlspecialchars($experience['company_name']) ?> - <?= htmlspecialchars($experience['position']) ?></h4>
                    <p><strong>From:</strong> <?= htmlspecialchars($experience['start_date']) ?> <strong>To:</strong> <?= htmlspecialchars($experience['end_date'] ?? 'Present') ?></p>
                    <p><?= nl2br(htmlspecialchars($experience['description'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No work experience available.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
