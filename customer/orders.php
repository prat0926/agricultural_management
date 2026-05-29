<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$sql = "SELECT o.*, 
        GROUP_CONCAT(CONCAT(oi.quantity, ' x ', p.product_name) SEPARATOR ', ') as items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.customer_id = $customer_id
        GROUP BY o.id
        ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo"><i class="fas fa-leaf"></i><span>FarmFresh</span></div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <br><br><br><br><h1>My Orders</h1>
            <p>Track your orders in real-time</p>
        </div>

        <?php if(mysqli_num_rows($orders) > 0): ?>
            <div class="orders-list">
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <div class="order-card" data-order-id="<?php echo $order['id']; ?>">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p class="tracking-id">Tracking ID: <?php echo $order['tracking_id']; ?></p>
                        </div>
                        <div class="order-status status-<?php echo $order['order_status']; ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="order-items">
                            <p><strong>Items:</strong> <?php echo $order['items']; ?></p>
                        </div>
                        <div class="order-details">
                            <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                            <p><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>
                        <div class="tracking-progress">
                            <div class="progress-step <?php echo $order['order_status'] != 'cancelled' ? 'completed' : ''; ?>">
                                <i class="fas fa-check-circle"></i>
                                <span>Order Placed</span>
                            </div>
                            <div class="progress-step <?php echo in_array($order['order_status'], ['confirmed','processing','shipped','delivered']) ? 'completed' : ($order['order_status'] == 'confirmed' ? 'active' : ''); ?>">
                                <i class="fas fa-check-circle"></i>
                                <span>Confirmed</span>
                            </div>
                            <div class="progress-step <?php echo in_array($order['order_status'], ['processing','shipped','delivered']) ? 'completed' : ($order['order_status'] == 'processing' ? 'active' : ''); ?>">
                                <i class="fas fa-box"></i>
                                <span>Processing</span>
                            </div>
                            <div class="progress-step <?php echo in_array($order['order_status'], ['shipped','delivered']) ? 'completed' : ($order['order_status'] == 'shipped' ? 'active' : ''); ?>">
                                <i class="fas fa-truck"></i>
                                <span>Shipped</span>
                            </div>
                            <div class="progress-step <?php echo $order['order_status'] == 'delivered' ? 'completed' : ''; ?>">
                                <i class="fas fa-home"></i>
                                <span>Delivered</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <p>No orders yet.</p>
                <a href="products.php" class="btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .orders-list {
            margin: 2rem 0;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 2rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .order-header h3 {
            margin: 0;
            color: white;
        }
        .tracking-id {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .status-pending { background: #ff9800; }
        .status-confirmed { background: #2196f3; }
        .status-processing { background: #9c27b0; }
        .status-shipped { background: #00bcd4; }
        .status-delivered { background: #4caf50; }
        .status-cancelled { background: #f44336; }
        .order-body {
            padding: 1.5rem;
        }
        .order-items, .order-details {
            margin-bottom: 1rem;
        }
        .tracking-progress {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
        }
        .progress-step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .progress-step i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
            color: #ddd;
        }
        .progress-step.completed i {
            color: #4caf50;
        }
        .progress-step.active i {
            color: #ff9800;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .progress-step span {
            font-size: 0.8rem;
            color: #666;
        }
        .empty-state {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 15px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #999;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .tracking-progress {
                flex-direction: column;
                gap: 1rem;
            }
            .progress-step {
                text-align: left;
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            .progress-step i {
                margin-bottom: 0;
            }
            .order-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }
    </style>

    <!-- Auto-refresh order status every 30 seconds -->
    <script>
        function refreshOrderStatus() {
            fetch('get_order_status.php')
                .then(response => response.json())
                .then(data => {
                    for (let orderId in data) {
                        let status = data[orderId];
                        let orderCard = document.querySelector(`.order-card[data-order-id='${orderId}']`);
                        if (orderCard) {
                            let statusBadge = orderCard.querySelector('.order-status');
                            if (statusBadge) {
                                // Update status text and class
                                statusBadge.className = `order-status status-${status}`;
                                statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                                
                                // Update progress steps
                                updateProgressSteps(orderCard, status);
                            }
                        }
                    }
                })
                .catch(err => console.log('Status refresh error:', err));
        }

        function updateProgressSteps(orderCard, status) {
            const steps = orderCard.querySelectorAll('.progress-step');
            // Reset all steps
            steps.forEach(step => {
                step.classList.remove('completed', 'active');
            });
            // Define order status progression
            const progression = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
            const currentIndex = progression.indexOf(status);
            for (let i = 0; i <= currentIndex; i++) {
                if (i < progression.length) {
                    steps[i].classList.add('completed');
                }
            }
            if (currentIndex >= 0 && currentIndex < progression.length - 1 && status !== 'delivered') {
                steps[currentIndex + 1]?.classList.add('active');
            }
        }

        // Refresh every 30 seconds
        setInterval(refreshOrderStatus, 30000);
        // Store previous statuses
let previousStatuses = {};

function checkForUpdates() {
    fetch('get_order_status.php')
        .then(response => response.json())
        .then(data => {
            for (let orderId in data) {
                let newStatus = data[orderId];
                if (previousStatuses[orderId] && previousStatuses[orderId] !== newStatus) {
                    // Status changed
                    showNotification(`Order #${orderId} status updated to ${newStatus.toUpperCase()}!`, 'info');
                }
                previousStatuses[orderId] = newStatus;
            }
            // Update UI as before
            for (let orderId in data) {
                let status = data[orderId];
                let orderCard = document.querySelector(`.order-card[data-order-id='${orderId}']`);
                if (orderCard) {
                    let statusBadge = orderCard.querySelector('.order-status');
                    if (statusBadge && statusBadge.textContent.toLowerCase() !== status) {
                        statusBadge.className = `order-status status-${status}`;
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        updateProgressSteps(orderCard, status);
                    }
                }
            }
        })
        .catch(err => console.log('Error:', err));
}

// Initialize previous statuses
fetch('get_order_status.php')
    .then(response => response.json())
    .then(data => { previousStatuses = data; });

// Check every 15 seconds
setInterval(checkForUpdates, 15000);
    </script>
</body>
</html>