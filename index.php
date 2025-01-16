<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Finder Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(124, 187, 221);
            background-image: url(image/image8.jpeg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
        
            text-align: center;
        }
        .navbar {
            position: sticky;
            top: 0;
            background-color: rgb(70, 113, 135);
            padding: 10px;
            text-align: right;
        }
        .navbar a {
            margin: 0 15px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .search-bar {
            width: 380px;
            padding: 10px;
            border: 1px solid rgb(70, 113, 135);
            border-radius: 5px;
            color: rgb(1, 43, 63);
            margin: 35px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="service.php">Services</a>
        <a href="#">Review & Ratings</a>
        <a href="login.php">Login</a>
        <a href="about_us.php">About Us</a>
    </div>

    <h2><font color="#ccebfc">Welcome to Local-Hand</font></h2>
    <form action="search.php" method="POST">
        <input type="text" name="search_query" class="search-bar" placeholder="Search work types...">
        <button type="submit">Search</button>
    </form>
</body>
</html>
