<?php
include 'db.php';  // Database connection

// Fetch categories from work_categories table
$categoryQuery = "SELECT * FROM work_categories";
$categories = $pdo->query($categoryQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch specialties grouped by category
$specialtyQuery = "SELECT * FROM specialties";
$specialtyResult = $pdo->query($specialtyQuery);

$specialties = [];
foreach ($specialtyResult as $row) {
    $specialties[$row['category_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Categories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Sidebar Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color:rgb(2, 6, 13);
           
        }

        .sidebar {
            width: 200px;
            background-color:rgb(146, 208, 194);
            padding: 10px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        .sidebar a {
            text-decoration: none;
            color: rgb(5, 14, 17);;
            display: block;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        .sidebar a:hover {
            background-color:rgb(155, 214, 246);
        }

        /* Main Content */
        .content {
            margin-left: 220px;
            padding: 20px;
        }

        /* Service Sections Grid Styling */
        .service-section {
            margin-bottom: 40px;
        }

        .service-images {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .service-item {
            text-align: center;
            width: 180px;
        }

        .service-item img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .service-item span {
            display: block;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>

    <script>
        $(document).ready(function () {
            $('.category-link').click(function (e) {
                e.preventDefault();
                const categoryId = $(this).data('category');

                $('html, body').animate({
                    scrollTop: $(`.service-section[data-category="${categoryId}"]`).offset().top - 20
                }, 800);
            });
        });
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Service Categories</h3>
        <?php foreach ($categories as $category) : ?>
            <a href="#" class="category-link" data-category="<?= $category['category_id'] ?>">
                <?= htmlspecialchars($category['category_name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Available Services</h2>
        
        <?php foreach ($specialties as $categoryId => $specialtyList) : ?>
            <div class="service-section" data-category="<?= $categoryId ?>">
            <h3><?= htmlspecialchars($categories[$categoryId - 1]['category_name']) ?></h3>

                <div class="service-images">
                    <?php foreach ($specialtyList as $specialty) : ?>
<div class="service-item">
<a href="worker_service.php?specialty_id=<?= htmlspecialchars($specialty['specialty_id']) ?>">
    <!-- Image with proper size and styling -->
    <img src="<?= htmlspecialchars($specialty['image']) ?>" 
         onerror="this.onerror=null; this.src='images/default.jpg'" 
        

    <!-- Service Name Displayed Once Here -->
    <span><?= htmlspecialchars($specialty['specialty_name']) ?></span>
</div>

                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
