<?php
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            background-color: rgb(149, 178, 207);
            background-image: url(image/user_dashboard.jpg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
        }
        .navbar {
            position: sticky;
            top: 0;
            background: rgb(108, 105, 180);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .main-content {
            text-align: center;
            padding: 35px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 40px auto;
        }
        .location-dropdown, .search-bar {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .location-dropdown {
            margin-right: 10px;
        }
        .search-btn {
            padding: 10px 20px;
            background:rgb(228, 143, 184);
            border: none;
            color: white;
            cursor: pointer;
        }
        .search-btn:hover {
            background:rgb(100, 125, 178);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="user_dashboard.php">Home</a>
            <a href="service.php">Services</a>
            <a href="book.php">Bookings</a>
            <a href="reviews.php">Feedback</a>
            <a href="user_profile.php">Profile</a>
            <a href="#">Help & Support</a>
        </div>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
    <h2><font color="#e5f3f0">Welcome to Local-Hand</font></h2>
        <form method="POST" class="search-container">
            <select name="location" class="location-dropdown">
                <option value="">Select Location</option>
                <?php
                // Fetch unique addresses (locations) from workers table
                $stmt = $pdo->query("SELECT DISTINCT address FROM workers WHERE address IS NOT NULL");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['address'] . "'>" . $row['address'] . "</option>";
                }
                ?>
            </select>
            <input type="text" name="search" class="search-bar" placeholder="Find your service here...">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>

        <?php
        if(isset($_POST['search'])) {
            // Prepare the search and location inputs safely with PDO
            $search = $_POST['search'];
            $location = $_POST['location'];

            $query = "SELECT * FROM workers WHERE specialty LIKE :search";
            if ($location) {
                $query .= " AND address = :location";
            }

            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            if ($location) {
                $stmt->bindValue(':location', $location, PDO::PARAM_STR);
            }

            $stmt->execute();
            echo "<h3>Search Results:</h3>";
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<p>" . $row['name'] . " - " . $row['specialty'] . "</p>";
            }
        }
        ?>
    </div>
</body>
</html>
