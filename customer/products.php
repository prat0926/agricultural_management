<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    $_SESSION['redirect_after_login'] = 'products.php';
    header("Location: ../customer_login.php");
    exit();
}

$current_date = date('Y-m-d');
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category && $category != 'all') {
    $sql = "SELECT p.*, u.username as farmer_name 
            FROM products p 
            JOIN users u ON p.farmer_id = u.id 
            WHERE p.expiry_date >= '$current_date' 
            AND p.status = 'available' 
            AND p.quantity > 0 
            AND p.category = '$category'
            ORDER BY p.created_at DESC";
} else {
    $sql = "SELECT p.*, u.username as farmer_name 
            FROM products p 
            JOIN users u ON p.farmer_id = u.id 
            WHERE p.expiry_date >= '$current_date' 
            AND p.status = 'available' 
            AND p.quantity > 0
            ORDER BY p.created_at DESC";
}
$products = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Products - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .products-header {
            text-align: center;
            margin-top: 100px;
            margin-bottom: 2rem;
        }
        .category-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        .filter-btn {
            padding: 8px 20px;
            background: white;
            border: 2px solid #4caf50;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #4caf50;
            color: white;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            height: 200px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-image i {
            font-size: 4rem;
            color: #999;
        }
        .product-info {
            padding: 1.5rem;
        }
        .product-info h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        .farmer-name {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4caf50;
            margin: 0.5rem 0;
        }
        .price span {
            font-size: 0.8rem;
            color: #666;
        }
        .expiry, .stock {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.3rem;
        }
        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background: #4caf50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        .add-to-cart-btn:hover {
            background: #45a049;
        }
        .floating-cart-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4caf50;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            text-decoration: none;
            z-index: 1000;
        }
        .cart-count-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f44336;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .no-products {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            grid-column: 1 / -1;
        }
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-leaf"></i>
                <span>FarmFresh</span>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart <span id="cart-count">0</span></a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="products-header">
            <h1>Fresh Farm Products</h1>
            <p>Directly from farmers to your table</p>
        </div>

        <!-- Category Filter -->
        <div class="category-filter">
            <button onclick="location.href='products.php'" class="filter-btn <?php echo !$category || $category == 'all' ? 'active' : ''; ?>">All</button>
            <button onclick="location.href='products.php?category=Vegetables'" class="filter-btn <?php echo $category == 'Vegetables' ? 'active' : ''; ?>">Vegetables</button>
            <button onclick="location.href='products.php?category=Fruits'" class="filter-btn <?php echo $category == 'Fruits' ? 'active' : ''; ?>">Fruits</button>
            <button onclick="location.href='products.php?category=Grains'" class="filter-btn <?php echo $category == 'Grains' ? 'active' : ''; ?>">Grains</button>
            <button onclick="location.href='products.php?category=Herbs'" class="filter-btn <?php echo $category == 'Herbs' ? 'active' : ''; ?>">Herbs</button>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if(mysqli_num_rows($products) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php 
                        // Check if product has an uploaded image and file exists
                        if(!empty($product['image']) && file_exists('../' . $product['image'])): 
                        ?>
                            <img src="../<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <?php else: ?>
                            <!-- Default icon if no image -->
                            <i class="fas fa-carrot"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="farmer-name">👨‍🌾 By: <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                        <div class="price">₹<?php echo $product['price']; ?> <span>/ <?php echo $product['unit']; ?></span></div>
                        <div class="expiry">📅 Expires: <?php echo date('d M Y', strtotime($product['expiry_date'])); ?></div>
                        <div class="stock">📦 Stock: <?php echo $product['quantity']; ?> <?php echo $product['unit']; ?></div>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="add-to-cart-btn">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>No products available</h3>
                    <p>Check back later for fresh produce!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <a href="cart.php" class="floating-cart-btn">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count-badge" id="floating-count">0</span>
    </a>

    <script src="../assets/js/main.js"></script>
    <script>
        // Update cart count in navbar and floating button
        function updateCartCountUI() {
            fetch('get_cart_count.php')
                .then(res => res.json())
                .then(data => {
                    let count = data.count || 0;
                    document.getElementById('cart-count').innerText = count;
                    document.getElementById('floating-count').innerText = count;
                })
                .catch(err => console.log('Cart count error', err));
        }
        setInterval(updateCartCountUI, 3000);
        updateCartCountUI();

        // addToCart function (if not already defined in main.js)
        if (typeof addToCart === 'undefined') {
            window.addToCart = function(productId) {
                let btn = event.target;
                if (btn.tagName !== 'BUTTON') btn = btn.closest('button');
                if (!btn) return;
                let originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                btn.disabled = true;

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Added to cart!', 'success');
                        updateCartCountUI();
                    } else {
                        showNotification(data.message || 'Error adding to cart', 'error');
                        if (data.redirect && confirm('Please login first. Go to login page?')) {
                            window.location.href = data.redirect;
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Network error. Please try again.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            };
        }

        function showNotification(msg, type) {
            let notif = document.createElement('div');
            notif.className = `notification notification-${type}`;
            notif.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}`;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 3000);
        }
    </script>
</body>
</html>