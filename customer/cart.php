<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    $_SESSION['redirect_after_login'] = 'cart.php';
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Process quantity updates (via form POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cart_id = (int)$_POST['cart_id'];
    $action = $_POST['action'];
    
    // Get current quantity
    $q_sql = "SELECT quantity FROM cart WHERE id = $cart_id AND customer_id = $customer_id";
    $q_res = mysqli_query($conn, $q_sql);
    if ($row = mysqli_fetch_assoc($q_res)) {
        $new_qty = $row['quantity'];
        if ($action === 'increase') {
            $new_qty++;
        } elseif ($action === 'decrease' && $new_qty > 1) {
            $new_qty--;
        }
        // Also check against product stock
        $stock_sql = "SELECT p.quantity as stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = $cart_id";
        $stock_res = mysqli_query($conn, $stock_sql);
        $stock_row = mysqli_fetch_assoc($stock_res);
        if ($new_qty <= $stock_row['stock']) {
            $update = "UPDATE cart SET quantity = $new_qty WHERE id = $cart_id";
            mysqli_query($conn, $update);
        }
    }
    header("Location: cart.php");
    exit();
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id AND customer_id = $customer_id");
    header("Location: cart.php");
    exit();
}

// Fetch cart items
$sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.product_name, p.price, p.unit, p.quantity as stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.customer_id = $customer_id";
$cart_items = mysqli_query($conn, $sql);
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - FarmFresh</title>
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
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart <span id="cart-count"></span></a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <br><br><br><br>
            <h1>Your Shopping Cart</h1>
        </div>

        <?php if(mysqli_num_rows($cart_items) > 0): ?>
        <div class="cart-container">
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($cart_items)): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td data-label="Product"><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                            <td data-label="Price">₹<?php echo $item['price']; ?>/<?php echo $item['unit']; ?></td>
                            <td data-label="Quantity">
                                <div class="quantity-control">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="qty-btn" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                    </form>
                                    <span class="qty-number"><?php echo $item['quantity']; ?></span>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="hidden" name="action" value="increase">
                                        <button type="submit" class="qty-btn" <?php echo $item['quantity'] >= $item['stock'] ? 'disabled' : ''; ?>>+</button>
                                    </form>
                                </div>
                            </td>
                            <td data-label="Subtotal">₹<?php echo number_format($subtotal, 2); ?></td>
                            <td data-label="Action"><a href="cart.php?remove=<?php echo $item['cart_id']; ?>" onclick="return confirm('Remove item?')" class="remove-btn"><i class="fas fa-trash"></i> Remove</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row"><td colspan="3"><strong>Total</strong></td><td colspan="2"><strong>₹<?php echo number_format($total, 2); ?></strong></td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="cart-actions">
                <a href="products.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                <a href="checkout.php" class="btn-primary">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p>Browse our products and add items to your cart</p>
            <a href="products.php" class="btn-primary">Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>

    <style>
    .quantity-control { display: flex; align-items: center; gap: 10px; }
    .qty-btn { width: 30px; height: 30px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5; cursor: pointer; font-size: 1.2rem; font-weight: bold; }
    .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .qty-number { min-width: 30px; text-align: center; font-weight: 500; }
    .cart-table td { vertical-align: middle; }
    .remove-btn { color: #f44336; text-decoration: none; }
    @media (max-width: 768px) {
        .quantity-control { justify-content: flex-start; }
        .cart-table td:before { content: attr(data-label); font-weight: 600; margin-right: 1rem; }
        .cart-table td { display: flex; justify-content: space-between; align-items: center; }
        .cart-table thead { display: none; }
    }
    </style>
    <script src="../assets/js/main.js"></script>
</body>
</html>