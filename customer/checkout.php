<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Get cart items
$sql = "SELECT c.*, p.product_name, p.price, p.farmer_id 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.customer_id = $customer_id";
$cart_items = mysqli_query($conn, $sql);

$total = 0;
while($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $tracking_id = 'FF' . strtoupper(uniqid());
    
    // Create order
    $order_sql = "INSERT INTO orders (customer_id, total_amount, payment_method, shipping_address, tracking_id, order_status) 
                  VALUES ($customer_id, $total, '$payment_method', '$address', '$tracking_id', 'pending')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Reset cart items pointer
        mysqli_data_seek($cart_items, 0);
        
        // Add order items
        while($item = mysqli_fetch_assoc($cart_items)) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
            mysqli_query($conn, $item_sql);
            
            // Update product quantity
            $update_product = "UPDATE products SET quantity = quantity - {$item['quantity']} WHERE id = {$item['product_id']}";
            mysqli_query($conn, $update_product);
        }
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE customer_id = $customer_id";
        mysqli_query($conn, $clear_cart);
        
        header("Location: order_success.php?id=$order_id");
        exit();
    }
}

// Reset cart items pointer for display
mysqli_data_seek($cart_items, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-leaf"></i>
                <span>FarmFresh</span>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Checkout</h1>
        </div>

        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <table>
                    <?php while($item = mysqli_fetch_assoc($cart_items)): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?> × <?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="total">
                        <td><strong>Total</strong></td>
                        <td><strong>₹<?php echo $total; ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="payment-form">
                <h3>Shipping & Payment</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" required placeholder="Enter your complete address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" required>
                            <option value="cod">Cash on Delivery</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <style>
    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin: 2rem 0;
    }
    .order-summary, .payment-form {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .order-summary table {
        width: 100%;
        margin-top: 1rem;
    }
    .order-summary td {
        padding: 0.5rem 0;
    }
    .total {
        border-top: 2px solid #e0e0e0;
        margin-top: 1rem;
        padding-top: 1rem;
    }
    .place-order-btn {
        width: 100%;
        padding: 12px;
        background: #4caf50;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
</body>
</html><?php
include '../config/database.php';

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    // Store the page they were trying to access
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Get cart items
$sql = "SELECT c.*, p.product_name, p.price, p.unit, p.farmer_id, p.quantity as stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.customer_id = $customer_id";
$cart_items = mysqli_query($conn, $sql);

// Check if cart is empty
if (mysqli_num_rows($cart_items) == 0) {
    header("Location: cart.php");
    exit();
}

$total = 0;
while($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $tracking_id = 'FF' . strtoupper(uniqid());
    
    // Create order
    $order_sql = "INSERT INTO orders (customer_id, total_amount, payment_method, shipping_address, tracking_id, order_status) 
                  VALUES ($customer_id, $total, '$payment_method', '$address', '$tracking_id', 'pending')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Reset cart items pointer
        mysqli_data_seek($cart_items, 0);
        
        // Add order items
        while($item = mysqli_fetch_assoc($cart_items)) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
            mysqli_query($conn, $item_sql);
            
            // Update product quantity
            $update_product = "UPDATE products SET quantity = quantity - {$item['quantity']} WHERE id = {$item['product_id']}";
            mysqli_query($conn, $update_product);
        }
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE customer_id = $customer_id";
        mysqli_query($conn, $clear_cart);
        
        header("Location: order_success.php?id=$order_id");
        exit();
    } else {
        $error = "Failed to place order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-leaf"></i>
                <span>FarmFresh</span>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your purchase</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php mysqli_data_seek($cart_items, 0); ?>
                <div class="summary-items">
                    <?php while($item = mysqli_fetch_assoc($cart_items)): ?>
                    <div class="summary-item">
                        <span>
                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                            <br><small>Quantity: <?php echo $item['quantity']; ?> <?php echo $item['unit']; ?></small>
                        </span>
                        <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="summary-total">
                    <span><strong>Total Amount</strong></span>
                    <span><strong>₹<?php echo number_format($total, 2); ?></strong></span>
                </div>
            </div>

            <div class="payment-form">
                <h3>Shipping Information</h3>
                <form method="POST" action="" id="checkoutForm">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly disabled>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" readonly disabled>
                    </div>
                    <div class="form-group">
                        <label>Delivery Address <span style="color: red;">*</span></label>
                        <textarea name="address" rows="3" required placeholder="Enter your complete address with PIN code"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Method <span style="color: red;">*</span></label>
                        <select name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cod">💵 Cash on Delivery</option>
                            <option value="card">💳 Credit/Debit Card</option>
                            <option value="upi">📱 UPI (Google Pay, PhonePe, etc.)</option>
                        </select>
                    </div>
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <style>
    .checkout-header {
        text-align: center;
        margin: 100px 0 2rem;
    }
    .checkout-header h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin: 2rem 0;
    }
    .order-summary, .payment-form {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .order-summary h3, .payment-form h3 {
        margin-bottom: 1.5rem;
        color: #333;
        border-bottom: 2px solid #4caf50;
        padding-bottom: 0.5rem;
        display: inline-block;
    }
    .summary-items {
        margin: 1rem 0;
        max-height: 300px;
        overflow-y: auto;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        margin-top: 1rem;
        border-top: 2px solid #e0e0e0;
        font-size: 1.2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #555;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
    }
    .form-group input:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
    }
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        resize: vertical;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: #4caf50;
    }
    .place-order-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #4caf50, #45a049);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .place-order-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76,175,80,0.3);
    }
    @media (max-width: 768px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
</body>
</html>