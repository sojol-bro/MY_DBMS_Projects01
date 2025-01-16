<?php
// Include the database connection
include 'db.php';
session_start();

// Check if the admin is logged in (use your own logic for this check)


// Handle form submissions (Remove Review, Remove Worker, Add Specialty Image)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove a Review
    if (isset($_POST['remove_review'])) {
        $review_id = $_POST['review_id'];
        if (is_numeric($review_id)) {
            $sql = "DELETE FROM reviews WHERE review_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$review_id]);
            echo "Review removed successfully.";
        } else {
            echo "Invalid review ID.";
        }
    }

    // Remove a Worker
    if (isset($_POST['remove_worker'])) {
        $worker_id = $_POST['worker_id'];
        if (is_numeric($worker_id)) {
            $sql = "DELETE FROM workers WHERE worker_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$worker_id]);
            echo "Worker removed successfully.";
        } else {
            echo "Invalid worker ID.";
        }
    }

    // Add an Image to Specialties
    if (isset($_POST['add_specialty_image'])) {
        $specialty_id = $_POST['specialty_id'];
        if (is_numeric($specialty_id) && isset($_FILES['image'])) {
            $image = $_FILES['image']['tmp_name'];
            $imageData = file_get_contents($image);
            $imageType = mime_content_type($image);

            // Check if the file is an image
            if (strpos($imageType, 'image') === false) {
                echo "Please upload a valid image.";
            } else {
                $sql = "UPDATE specialties SET image = ? WHERE specialty_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$imageData, $specialty_id]);
                echo "Image added to specialty successfully.";
            }
        } else {
            echo "Invalid specialty ID or image file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Actions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(78, 110, 143); /* Light background color for the page */
        }
        .navbar {
            position: sticky;
            top: 0;
            background-color:rgb(155, 193, 231); /* Dark background for the navbar */
            padding: 10px;
        }
        .navbar a {
            color: black;
            text-decoration: none;
            padding: 10px 20px;
        }
        .navbar a:hover {
            background-color: #575d63;
            border-radius: 4px;
        }
        .container {
            margin-top: 30px;
            background-color: #fff; /* White background for the content */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <a href="admin_dashboard.php" class="navbar-brand">Home</a>
    <a href="admin_actions.php" class="navbar-brand">Admin Actions</a>
</nav>

<div class="container">
    <h2>Admin Actions</h2>

    <!-- Form to Remove a Review -->
    <form method="POST" class="mb-4">
        <h4>Remove a Review</h4>
        <div class="form-group">
            <label>Review ID:</label>
            <input type="number" name="review_id" class="form-control" required>
        </div>
        <button type="submit" name="remove_review" class="btn btn-danger">Remove Review</button>
    </form>

    <!-- Form to Remove a Worker -->
    <form method="POST" class="mb-4">
        <h4>Remove a Worker</h4>
        <div class="form-group">
            <label>Worker ID:</label>
            <input type="number" name="worker_id" class="form-control" required>
        </div>
        <button type="submit" name="remove_worker" class="btn btn-danger">Remove Worker</button>
    </form>

    <!-- Form to Add Image to Specialties -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <h4>Add Image to Specialties</h4>
        <div class="form-group">
            <label>Specialty ID:</label>
            <input type="number" name="specialty_id" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Image:</label>
            <input type="file" name="image" class="form-control-file" required>
        </div>
        <button type="submit" name="add_specialty_image" class="btn btn-primary">Add Image</button>
    </form>
</div>

</body>
</html>
