<?php
session_start();
include 'db.php'; // Ensure this file contains the $pdo connection

// Initialize message variable
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'user' or 'worker'

    try {
        // Ensure $pdo is being used correctly
        if ($role === 'worker') {
            $stmt = $pdo->prepare("SELECT * FROM workers WHERE email = ?");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        }

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($role === 'worker') {
                    $_SESSION['worker_id'] = $user['worker_id'];
                    header("Location: worker_dashboard.php");
                    exit();
                } else {
                    if($email=='admin@gmail.com')
                    {$_SESSION['user_id'] = $user['user_id'];
                        header("Location: admin_dashboard.php");
                        exit();}
                    $_SESSION['user_id'] = $user['user_id'];
                    header("Location: user_dashboard.php");
                    exit();
                }
            } else {
                $message = "<p style='color: red;'>Invalid password.</p>";
            }
        } else {
            $message = "<p style='color: red;'>Invalid email or user does not exist.</p>";
        }
    } catch (PDOException $e) {
        $message = "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color:rgb(167, 188, 209);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background:rgb(196, 205, 234);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .login-image {
            background-color:rgb(73, 82, 187);
            color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-image h2 {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .login-image p {
            font-size: 16px;
        }

        .login-form {
            flex: 1;
            padding: 40px;
        }

        .login-form h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
        }

        .form-group input:focus {
            border-color: #0056d2;
            outline: none;
        }

        .form-group select {
            background-color: #fff;
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #0056d2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-button:hover {
            background-color: #0041a8;
        }

        .additional-links {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .additional-links a {
            color: #0056d2;
            text-decoration: none;
        }

        .additional-links a:hover {
            text-decoration: underline;
        }
        
        .message {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-image">
            <h2>Welcome Back!</h2>
            <p>Login to continue</p>
        </div>
        <div class="login-form">
            <h2>Hello Again!</h2>
            <!-- Display error message here -->
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="worker">Worker</option>
                    </select>
                </div>
                <button type="submit" class="login-button">Login</button>
                <div class="additional-links">
                    <a href="#">Forgot Password?</a>
                    <a href="signup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
