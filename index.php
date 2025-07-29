<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require 'db.php';

$stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$wallet = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easypaisa Clone - Home</title>
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
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .wallet-balance {
            background: #00c4b4;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .service-card {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .service-card:hover {
            transform: scale(1.05);
        }
        .service-card h3 {
            color: #00c4b4;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }
            .services {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
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
        <div class="wallet-balance">
            Wallet Balance: PKR <?php echo number_format($wallet['balance'], 2); ?>
        </div>
        <div class="services">
            <div class="service-card" onclick="redirectTo('wallet.php')">
                <h3>Wallet</h3>
                <p>Manage your digital funds</p>
            </div>
            <div class="service-card" onclick="redirectTo('transfer.php')">
                <h3>Money Transfer</h3>
                <p>Send money instantly</p>
            </div>
            <div class="service-card" onclick="redirectTo('bills.php')">
                <h3>Bill Payments</h3>
                <p>Pay utilities & recharge</p>
            </div>
            <div class="service-card" onclick="redirectTo('history.php')">
                <h3>Transaction History</h3>
                <p>View all transactions</p>
            </div>
        </div>
    </div>
</body>
</html>
