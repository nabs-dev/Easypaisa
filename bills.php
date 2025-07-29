<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
require 'db.php';

$providers = ['KE', 'LESCO', 'Jazz', 'Telenor', 'PTCL', 'SNGPL'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider = $_POST['provider'];
    $bill_type = $_POST['bill_type'];
    $amount = $_POST['amount'];
    $reference = $_POST['reference'];
    $pin = $_POST['pin'];

    $stmt = $conn->prepare("SELECT pin, balance FROM users u JOIN wallets w ON u.id = w.user_id WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (password_verify($pin, $user['pin']) && $user['balance'] >= $amount) {
        $conn->beginTransaction();
        try {
            $stmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ?");
            $stmt->execute([$amount, $_SESSION['user_id']]);
            
            $stmt = $conn->prepare("INSERT INTO bill_payments (user_id, provider, bill_type, amount, reference_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $provider, $bill_type, $amount, $reference]);
            
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'bill_payment', ?, 'completed')");
            $stmt->execute([$_SESSION['user_id'], $amount]);
            
            $conn->commit();
            echo "<script>alert('Bill payment successful!'); window.location.href='bills.php';</script>";
        } catch(Exception $e) {
            $conn->rollBack();
            echo "<script>alert('Payment failed: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid PIN or insufficient balance');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills - Easypaisa Clone</title>
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
        select, input, button {
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
        <h2>Bill Payments</h2>
        <form method="POST">
            <select name="provider" required>
                <?php foreach ($providers as $provider): ?>
                    <option value="<?php echo $provider; ?>"><?php echo $provider; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="bill_type" required>
                <option value="electricity">Electricity</option>
                <option value="gas">Gas</option>
                <option value="water">Water</option>
                <option value="mobile">Mobile Recharge</option>
                <option value="internet">Internet</option>
            </select>
            <input type="number" name="amount" placeholder="Amount" required>
            <input type="text" name="reference" placeholder="Reference Number" required>
            <input type="password" name="pin" placeholder="Enter PIN" required>
            <button type="submit">Pay Bill</button>
        </form>
    </div>
</body>
</html>
