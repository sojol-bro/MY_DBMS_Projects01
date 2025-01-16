<?php
include 'db.php';
session_start();
/*if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Sticky Navbar */
        .navbar {
            background-color: #d7f3ed; /* Dark background color */
            position: sticky;
            top: 0;
            width: 100%;
            padding: 10px;
            z-index: 1000; /* To ensure navbar stays on top */
            color: white;
            display: flex;
            justify-content: flex-end; /* Align items to the right */
            align-items: center;
        }

        .navbar-left {
            display: flex;
            gap: 30px;
            margin-right: 20px; /* Adds space from the right edge */
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Body background color */
        body {
            background-color: rgb(191, 192, 237); /* Light background color */
            font-family: Arial, sans-serif;
            padding-top: 60px; /* To prevent content from hiding under the sticky navbar */
        }

        h1, h2 {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-left">
        <a href="worker_approval.php">Worker Approval</a>
            <a href="review_handle.php">Review Handle</a>
            <a href="category_set.php">Category</a>
       
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1>Welcome, Admin</h1>

    <!-- Main body is empty -->

</body>
</html>
