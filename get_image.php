<?php
include 'db.php';  // Ensure this connects to your database

if (isset($_GET['specialty_id'])) {
    $specialty_id = (int)$_GET['specialty_id'];

    // Query the database to get the image and its format (you may store the format in another column if necessary)
    $stmt = $pdo->prepare("SELECT image, image_extension FROM specialties WHERE specialty_id = ?");
    $stmt->execute([$specialty_id]);
    $specialty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($specialty) {
        // Get the image extension from the database (this assumes you store the extension in a separate column, e.g., image_extension)
        $extension = strtolower($specialty['image_extension']);  // Use lowercase for consistency

        // Set the correct Content-Type based on the image extension
        switch ($extension) {
            case 'jpg':  header("Content-Type: image/jpg");
            break;
            case 'jpeg':
                header("Content-Type: image/jpeg");
                break;
            case 'png':
                header("Content-Type: image/png");
                break;
            case 'webp':
                header("Content-Type: image/webp");
                break;
            default:
                // If the extension is not recognized, serve a default image or return an error
                header("HTTP/1.1 415 Unsupported Media Type");
                echo "Unsupported image format.";
                exit;
        }

        // Output the binary data of the image
        echo $specialty['image'];
        exit;
    } else {
        // If specialty is not found
        header("HTTP/1.1 404 Not Found");
        echo "Image not found.";
        exit;
    }
}
?>
