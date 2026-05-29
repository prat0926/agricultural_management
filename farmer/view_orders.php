<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $allowed_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($new_status, $allowed_statuses)) {
        $update_sql = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";
        mysqli_query($conn, $update_sql);
    }
    header("Location: view_orders.php");
    exit();
}

// Get orders for farmer's products
$sql = "SELECT o.id, o.order_date, o.total_amount, o.order_status, o.payment_method, 
        o.shipping_address, o.tracking_id, u.username as customer_name, u.phone, u.email,
        GROUP_CONCAT(CONCAT(oi.quantity, ' x ', p.product_name) SEPARATOR ', ') as items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN users u ON o.customer_id = u.id
        WHERE p.farmer_id = $farmer_id
        GROUP BY o.id
        ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo"><i class="fas fa-leaf"></i><span>FarmFresh</span></div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="view_orders.php">Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Customer Orders</h1>
            <p>Update order status to keep customers informed</p>
        </div>

        <div class="orders-list">
            <?php if(mysqli_num_rows($orders) > 0): ?>
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p>Tracking ID: <?php echo $order['tracking_id']; ?></p>
                            <p>Placed on: <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div class="order-status-badge status-<?php echo $order['order_status']; ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </div>
                    </div>
                    <div class="order-details">
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo $order['email']; ?>, <?php echo $order['phone']; ?>)</p>
                        <p><strong>Items:</strong> <?php echo $order['items']; ?></p>
                        <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                        <p><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    </div>
                    <div class="order-status-update">
                        <form method="POST" action="" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <label>Update Status:</label>
                            <select name="order_status" class="status-select">
                                <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $order['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="update-status-btn">Update</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No orders received yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .dashboard-header {
            margin: 100px 0 2rem;
            text-align: center;
        }
        .orders-list {
            margin: 2rem 0;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 2rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        .order-status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .status-pending { background: #ff9800; color: white; }
        .status-confirmed { background: #2196f3; color: white; }
        .status-processing { background: #9c27b0; color: white; }
        .status-shipped { background: #00bcd4; color: white; }
        .status-delivered { background: #4caf50; color: white; }
        .status-cancelled { background: #f44336; color: white; }
        .order-details p {
            margin: 0.5rem 0;
        }
        .order-status-update {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
        }
        .status-form {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .status-select {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }
        .update-status-btn {
            padding: 8px 20px;
            background: #4caf50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .update-status-btn:hover {
            background: #45a049;
        }
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .order-status-update {
                justify-content: flex-start;
            }
        }
    </style>
</body>
</html>