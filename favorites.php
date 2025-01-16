<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $worker_id = $_POST['worker_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, worker_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $worker_id]);
        echo "Worker added to favorites!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT f.favorite_id, w.name, w.specialty 
                               FROM favorites f 
                               JOIN workers w ON f.worker_id = w.worker_id 
                               WHERE f.user_id = ?");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($favorites);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
