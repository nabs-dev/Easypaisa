<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require 'db.php';

$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - Easypaisa Clone</title>
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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #00c4b4;
            color: white;
        }
        tr:nth-child(even) {
            background: #f5f5f5;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }
            table {
                font-size: 14px;
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
        <h2>Transaction History</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Recipient</th>
                <th>Status</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['created_at']; ?></td>
                    <td><?php echo ucfirst($transaction['type']); ?></td>
                    <td>PKR <?php echo number_format($transaction['amount'], 2); ?></td>
                    <td><?php echo $transaction['recipient'] ?: '-'; ?></td>
                    <td><?php echo ucfirst($transaction['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
