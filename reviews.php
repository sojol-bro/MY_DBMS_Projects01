<?php
session_start();
include 'db.php';  // Using PDO now

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id'];
$feedback = "";
$worker_list = [];

// Fetch workers whom the user has already booked
try {
    $stmt = $pdo->prepare("SELECT DISTINCT w.worker_id, w.name FROM localhand.bookings b JOIN localhand.workers w ON b.worker_id = w.worker_id WHERE b.user_id = ?");
    $stmt->execute([$user_id]);
    $worker_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching workers: " . $e->getMessage());
}

// Handle feedback submission using PDO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_feedback'])) {
    $worker_id = $_POST['worker_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['review_text'];

    if (!empty($worker_id) && !empty($rating) && !empty($comment)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO localhand.reviews (user_id, worker_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $worker_id, $rating, $comment]);
            $feedback = "Feedback submitted successfully!";
        } catch (PDOException $e) {
            $feedback = "Error submitting feedback: " . $e->getMessage();
        }
    } else {
        $feedback = "Please fill in all fields.";
    }
}

// Fetch user feedback history using PDO
$feedback_history = [];
try {
    $stmt = $pdo->prepare("SELECT r.rating, r.review_text, r.created_at, w.name AS worker_name FROM localhand.reviews r JOIN localhand.workers w ON r.worker_id = w.worker_id WHERE r.user_id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$user_id]);
    $feedback_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching feedback history: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(151, 197, 211);
            display: flex;
            height: 100vh;
        }
        

        .container {
            margin: auto;
            padding: 18px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 60%;
        }

        .feedback-form, .feedback-history {
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
        }

        input, textarea, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color:rgb(132, 174, 215);
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color:rgb(174, 204, 243);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
        }

        .feedback-message {
            margin-bottom: 20px;
            padding: 10px;
            color: white;
            text-align: center;
            border-radius: 5px;
        }

        .feedback-message.success {
            background-color: #4CAF50;
        }

        .feedback-message.error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Give Feedback</h2>
        <?php if (!empty($feedback)): ?>
            <div class="feedback-message <?= strpos($feedback, 'successfully') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($feedback) ?>
            </div>
        <?php endif; ?>

        <div class="feedback-form">
            <form method="POST">
                <label for="worker_id">Select Worker:</label>
                <select name="worker_id" id="worker_id" required>
                    <option value="">-- Choose a Worker --</option>
                    <?php foreach ($worker_list as $worker): ?>
                        <option value="<?= $worker['worker_id'] ?>">
                            <?= htmlspecialchars($worker['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" step="0.1" required>

                <label for="review_text">Comment:</label>
                <textarea name="review_text" id="review_text" rows="5" required></textarea>

                <button type="submit" name="submit_feedback">Submit Feedback</button>
            </form>
        </div>

        <div class="feedback-history">
            <h2>Your Feedback History</h2>
            <?php if (!empty($feedback_history)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Worker Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedback_history as $entry): ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['worker_name']) ?></td>
                                <td><?= htmlspecialchars($entry['rating']) ?></td>
                                <td><?= htmlspecialchars($entry['review_text']) ?></td>
                                <td><?= htmlspecialchars($entry['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback history available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
