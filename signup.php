<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $pin = $_POST['pin'];

    // Validate inputs
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($pin)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif (strlen($pin) != 4 || !ctype_digit($pin)) {
        echo "<script>alert('PIN must be a 4-digit number.');</script>";
    } else {
        // Check for duplicates
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR phone = ?");
        $stmt->execute([$username, $email, $phone]);
        if ($stmt->fetch()) {
            echo "<script>alert('Username, email, or phone number already exists.');</script>";
        } else {
            try {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $pin_hash = password_hash($pin, PASSWORD_BCRYPT);

                // Insert user
                $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, pin) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $phone, $password_hash, $pin_hash]);
                
                $user_id = $conn->lastInsertId();
                
                // Initialize wallet with $1,000,000
                $stmt = $conn->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, 1000000.00)");
                $stmt->execute([$user_id]);
                
                // Record initial deposit
                $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'deposit', ?, 'completed')");
                $stmt->execute([$user_id, 1000000.00]);
                
                echo "<script>alert('Signup successful! Welcome bonus of $1,000,000 added to your wallet.'); window.location.href='login.php';</script>";
            } catch(PDOException $e) {
                echo "<script>alert('Signup failed: " . htmlspecialchars($e->getMessage()) . "');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Easypaisa Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #00c4b4, #7b2ff7);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #00c4b4;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #00c4b4;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #009a8a;
        }
        a {
            color: #00c4b4;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="pin" placeholder="4-digit PIN" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
