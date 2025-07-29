<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pin = password_hash($_POST['pin'], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, pin = ? WHERE id = ?");
        $stmt->execute([$email, $phone, $pin, $_SESSION['user_id']]);
        echo "<script>alert('Account updated successfully!'); window.location.href='account.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

$stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Easypaisa Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #00c4b4, #7b2ff7);
            margin: 0;
            color: #333;
        }
        .navbar {
            background: #fff;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #00c4b4;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            color: #009a8a;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, button {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #00c4b4;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #009a8a;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Home</a>
            <a href="wallet.php">Wallet</a>
            <a href="transfer.php">Transfer</a>
            <a href="bills.php">Bills</a>
            <a href="history.php">History</a>
            <a href="account.php">Account</a>
        </div>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <h2>Account Settings</h2>
        <form method="POST">
            <input type="text" name="username" value="<?php echo $user['username']; ?>" disabled>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
            <input type="password" name="pin" placeholder="New PIN" required>
            <button type="submit">Update Account</button>
        </form>
    </div>
</body>
</html>
