<?php
// Include the database connection file
include 'db.php';

// Start session (assuming admin login is required)


// Fetch all categories/specialties from the database
$categories = $pdo->query("SELECT * FROM specialties")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialty_name = $_POST['specialty_name'] ?? '';
    $specialty_id = $_POST['specialty_id'] ?? null;

    // Handle image upload
    // Handle image upload with support for multiple formats
$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $file_type = mime_content_type($_FILES['image']['tmp_name']); // Get actual MIME type

    if (in_array($file_type, $allowed_types)) {
        $target_dir = __DIR__ . "/uploads/";
        $file_name = uniqid() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $file_name;

        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = "uploads/" . $file_name;
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    } else {
        echo "<script>alert('Invalid image format. Only JPG, JPEG, PNG, and WEBP are allowed.');</script>";
        exit;
    }
}


    // Retain existing image if no new one is uploaded
    if ($specialty_id && empty($image_path)) {
        $stmt = $pdo->prepare("SELECT image FROM specialties WHERE specialty_id = ?");
        $stmt->execute([$specialty_id]);
        $image_path = $stmt->fetchColumn();
    }

    // Insert or update category details in the database
    if ($specialty_id) {
        // Update existing category
        $sql = "UPDATE specialties SET specialty_name = ?, image = ? WHERE specialty_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$specialty_name, $image_path, $specialty_id]);
    } else {
        // Insert new category
        $sql = "INSERT INTO specialties (specialty_name, image) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$specialty_name, $image_path]);
    }
    echo "<script>alert('Category saved successfully!');</script>";
    header("Location: category_set.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: rgb(240, 240, 240);
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            margin-bottom: 20px;
        }
        .category-img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Category Management</h1>

    <div class="row">
        <div class="col-md-4">
            <h3>Add / Update Category</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="specialty_name">Name</label>
                    <input type="text" id="specialty_name" name="specialty_name" class="form-control" required>
                </div>
               
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" class="form-control-file">
                </div>
                <input type="hidden" name="specialty_id" id="specialty_id">
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>

        <div class="col-md-8">
            <h3>Existing Categories</h3>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <img src="<?= htmlspecialchars($category['image']) ?>" alt="Category Image" class="card-img-top category-img">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($category['specialty_name']) ?></h5>
                                
                                <button class="btn btn-sm btn-warning edit-category" 
                                        data-id="<?= $category['specialty_id'] ?>" 
                                        data-name="<?= htmlspecialchars($category['specialty_name']) ?>" 
                                       
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.edit-category').forEach(button => {
        button.addEventListener('click', function() {
            const specialtyId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');

            document.getElementById('specialty_id').value = specialtyId;
            document.getElementById('specialty_name').value = name;
          
        });
    });
</script>
</body>
</html>
