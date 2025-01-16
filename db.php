<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "localhand";


try {
    // Correct way to include the port in the DSN string
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
