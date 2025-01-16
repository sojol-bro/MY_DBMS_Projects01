<?php
include 'db.php';
$message='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $role = $_POST['role']; // 'user' or 'worker'

    try {
        if ($role === 'worker') {
            $address = $_POST['address'];
            $specialty = $_POST['specialty'];
            $stmt = $pdo->prepare("INSERT INTO workers (name, phone, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $password]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $phone, $role]);
        }
        $message = "<p style='color: green;'>Registration successful!</p>";
    } catch (PDOException $e) {
        $message = "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

$roleSelected = isset($_POST['role']) ? $_POST['role'] : 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color:rgb(163, 165, 228);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-container {
            display: flex;
            background: rgb(194, 207, 245);;
            max-width: 750px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(33, 18, 18, 0.1);
        }

        .signup-image {
            flex: 1;
            background-color:rgb(101, 149, 216);
            background-size: cover;
            background-position: center;
            color: rgb(12, 24, 38);;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .signup-image h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .signup-image p {
            font-size: 14px;
        }

        .signup-form {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .signup-form h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #0056d2;
            outline: none;
        }

        .signup-button {
            width: 100%;
            padding: 10px;
            background-color: #0056d2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 8px;
        }

        .signup-button:hover {
            background-color: #0041a8;
        }

        .additional-links {
            margin-top: 8px;
            text-align: center;
        }

        .additional-links a {
            color: #0056d2;
            text-decoration: none;
        }

        .additional-links a:hover {
            text-decoration: underline;
        }

        #worker-fields {
            display: <?php echo ($roleSelected === 'worker') ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-image">
            <h2>Welcome!</h2>
            <p>Join our community by creating your account.</p>
        </div>
        <div class="signup-form">
            <h2>Sign Up</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="user" <?php echo ($roleSelected === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="worker" <?php echo ($roleSelected === 'worker') ? 'selected' : ''; ?>>Worker</option>
                    </select>
                </div>
                <div class="form-group" id="worker-fields">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address">
                    <label for="specialty">Specialty:</label>
                    <input type="text" id="specialty" name="specialty">
                </div>
                <button type="submit" class="signup-button">Sign Up</button>
                <div class="additional-links">
                    <a href="login.php">Already have an account? Login</a>
                </div>
            </form>
            <div class="message">
                <?php echo $message; ?>
            </div>
        </div>
    </div>
</body>
</html>
