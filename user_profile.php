<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

// Convert the image BLOB to a data URL for displaying in the HTML
$profile_image = $user['image'] ? 'data:image/jpeg;base64,' . base64_encode($user['image']) : 'https://via.placeholder.com/150';

// Handle Edit Profile form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error_message = "All fields are required.";
    } else {
        // Update user data in the database
        $update_query = "UPDATE users SET name = :name, email = :email, phone = :phone, address = :address WHERE user_id = :user_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $update_stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $update_stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            header('Location: user_profile.php');
            exit();
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
    }
}

// Handle Upload Photo form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        // Check file size (limit to 2MB)
        if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
            $error_message = "File size exceeds 2MB. Please upload a smaller file.";
        } else {
            $file_tmp = $_FILES['profile_photo']['tmp_name'];
            $file_content = file_get_contents($file_tmp);

            // Update photo in the database
            $photo_query = "UPDATE users SET image = :image WHERE user_id = :user_id";
            $photo_stmt = $pdo->prepare($photo_query);
            $photo_stmt->bindParam(':image', $file_content, PDO::PARAM_LOB);
            $photo_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($photo_stmt->execute()) {
                $success_message = "Photo uploaded successfully!";
                header('Location: user_profile.php');
                exit();
            } else {
                $error_message = "Failed to upload photo. Please try again.";
            }
        }
    } else {
        $error_message = "Please select a valid photo.";
    }
}



// Fetch booking history
$booking_query = "SELECT * FROM bookings WHERE user_id = :user_id ORDER BY booking_date DESC";
$booking_stmt = $pdo->prepare($booking_query);
$booking_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$booking_stmt->execute();
$booking_result = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch feedback history
$feedback_query = "SELECT * FROM reviews WHERE user_id = :user_id ORDER BY created_at DESC";
$feedback_stmt = $pdo->prepare($feedback_query);
$feedback_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #333;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #333;
        }
        .profile-panel, .upload-photo-panel, .booking-history-panel, .feedback-history-panel {
            display: none;
        }
        .upload-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .upload-button:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        // Function to toggle visibility of different panels
        function togglePanel(panelId) {
            var panels = document.querySelectorAll('.profile-panel, .upload-photo-panel, .booking-history-panel, .feedback-history-panel');
            panels.forEach(function(panel) {
                panel.style.display = 'none'; // Hide all panels
            });

            var activePanel = document.getElementById(panelId);
            if (activePanel) {
                activePanel.style.display = 'block'; // Show the clicked panel
            }
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>User Profile</h2>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="javascript:void(0);" onclick="togglePanel('profile-panel')">Edit Profile</a>
        <a href="javascript:void(0);" onclick="togglePanel('upload-photo-panel')">Upload Photo</a>
        <a href="javascript:void(0);" onclick="togglePanel('booking-history-panel')">Booking History</a>
        <a href="javascript:void(0);" onclick="togglePanel('feedback-history-panel')">Feedback History</a>
    </div>

    <div class="main-content">
        <div class="profile-header">
            <img src="<?php echo $profile_image; ?>" alt="Profile Photo">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <?php if (isset($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
                <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
            </div>
        </div>

        <!-- Edit Profile Panel -->
        <div id="profile-panel" class="profile-panel">
            <h2>Edit Profile</h2>
            <form action="" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br>
                <button type="submit" name="edit_profile">Save Changes</button>
            </form>
        </div>

        <!-- Upload Photo Panel -->
        <div id="upload-photo-panel" class="upload-photo-panel">
            <h2>Upload Profile Photo</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="profile_photo">Choose a photo to upload:</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
                <button class="upload-button" type="submit" name="upload_photo">Upload Photo</button>
            </form>
        </div>

        <!-- Booking History Panel -->
        <div id="booking-history-panel" class="booking-history-panel">
            <h2>Booking History</h2>
            <?php if (empty($booking_result)): ?>
                <p>No booking history found.</p>
            <?php else: ?>
                <?php foreach ($booking_result as $booking): ?>
                    <p>Booking ID: <?php echo $booking['booking_id']; ?> | Date: <?php echo $booking['booking_date']; ?> | Status: <?php echo $booking['status']; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Feedback History Panel -->
        <div id="feedback-history-panel" class="feedback-history-panel">
            <h2>Feedback History</h2>
            <?php if (empty($feedback_result)): ?>
                <p>No feedback history found.</p>
            <?php else: ?>
                <?php foreach ($feedback_result as $feedback): ?>
                    <p>Review ID: <?php echo $feedback['review_id']; ?> | Rating: <?php echo $feedback['rating']; ?> | Feedback: <?php echo $feedback['review_text']; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
