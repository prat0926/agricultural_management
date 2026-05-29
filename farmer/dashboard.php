<?php
// farmer/dashboard.php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../farmer_login.php");
    exit();
}

// Get farmer's products
$farmer_id = $_SESSION['user_id'];
$sql = "SELECT * FROM products WHERE farmer_id = $farmer_id ORDER BY created_at DESC";
$products = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - FarmFresh</title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="view_orders.php">Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Welcome, Farmer <?php echo $_SESSION['username']; ?>!</h1>
            <a href="add_product.php" class="btn-primary">+ Add New Product</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3>Total Products</h3>
                <p><?php echo mysqli_num_rows($products); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Orders</h3>
                <p>0</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-rupee-sign"></i>
                <h3>Total Earnings</h3>
                <p>₹0</p>
            </div>
        </div>

        <div class="products-list">
            <h2>Your Products</h2>
            <div class="product-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td>₹<?php echo $product['price']; ?>/<?php echo $product['unit']; ?></td>
                            <td><?php echo $product['quantity']; ?> <?php echo $product['unit']; ?></td>
                            <td><?php echo date('d M Y', strtotime($product['expiry_date'])); ?></td>
                            <td>
                                <span class="status <?php echo $product['status']; ?>">
                                    <?php echo $product['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>