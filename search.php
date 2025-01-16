<?php
include 'db.php';

if (isset($_POST['search_query'])) {
    // Using prepared statements to prevent SQL injection
    $search_query = '%' . $_POST['search_query'] . '%';
    $sql = "SELECT * FROM workers WHERE specialty LIKE :search_query";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_query', $search_query, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<p>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['specialty']) . "</p>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
}
$pdo = null; // Closing the connection
?>
