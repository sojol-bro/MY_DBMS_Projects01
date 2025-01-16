<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Post a new job request
    $user_id = $_POST['user_id'];
    $worker_id = $_POST['worker_id'];
    $job_description = $_POST['job_description'];
    $budget = $_POST['budget'];

    try {
        $stmt = $pdo->prepare("INSERT INTO job_requests (user_id, worker_id, job_description, budget) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $worker_id, $job_description, $budget]);
        echo "Job request posted successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    // Fetch job requests for a user
    $user_id = $_GET['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM job_requests WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $job_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($job_requests);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
