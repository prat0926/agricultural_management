<?php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../customer_login.php");
    exit();
}

$order_id = isset($_GET['id']) ? $_GET['id'] : 0;
$customer_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE id = $order_id AND customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for shopping with FarmFresh</p>
            
            <div class="order-info">
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Tracking ID:</strong> <?php echo $order['tracking_id']; ?></p>
                <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                <p><strong>Order Status:</strong> <span class="status-badge"><?php echo ucfirst($order['order_status']); ?></span></p>
            </div>

            <div class="action-buttons">
                <a href="orders.php" class="btn-primary">View My Orders</a>
                <a href="dashboard.php" class="btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </div>

    <style>
    .success-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }
    .success-card {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .success-icon {
        font-size: 5rem;
        color: #4caf50;
        margin-bottom: 1rem;
    }
    .order-info {
        background: #f5f5f5;
        padding: 1.5rem;
        border-radius: 10px;
        margin: 2rem 0;
        text-align: left;
    }
    .order-info p {
        margin: 0.5rem 0;
    }
    .status-badge {
        background: #4caf50;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    .action-buttons a {
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 8px;
    }
    </style>
</body>
</html>