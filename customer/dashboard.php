<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    $_SESSION['redirect_after_login'] = 'dashboard.php';
    header("Location: ../customer_login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Dashboard card styles */
        .dashboard-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 50px;
        }
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s ease;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #2c5f2d, #1a3b1a);
            color: white;
            padding: 2rem;
        }
        .card-header i {
            font-size: 4rem;
            margin-bottom: 0.5rem;
        }
        .card-header h1 {
            font-size: 1.8rem;
            margin: 0;
        }
        .card-header p {
            margin-top: 0.5rem;
            opacity: 0.9;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-shop {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .btn-shop i {
            margin-right: 8px;
        }
        .btn-shop:hover {
            background: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .logout-link {
            color: #f44336;
            text-decoration: none;
            display: inline-block;
            margin-top: 0.5rem;
            transition: color 0.3s;
        }
        .logout-link:hover {
            color: #d32f2f;
            text-decoration: underline;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navbar (exactly like other pages) -->
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

    <!-- Dashboard content -->
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-seedling"></i>
                <h1>Welcome, <?php echo $username; ?>!</h1>
                <p>Fresh vegetables, straight from the farm</p>
            </div>
            <div class="card-body">
                <a href="products.php" class="btn-shop">
                    <i class="fas fa-shopping-basket"></i> Browse Products
                </a>
                <br>
                <a href="../logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Update cart count in navbar
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(res => res.json())
                .then(data => {
                    let count = data.count || 0;
                    let cartSpan = document.getElementById('cart-count');
                    if (cartSpan) cartSpan.innerText = count;
                })
                .catch(err => console.log('Cart count error', err));
        }
        updateCartCount();
    </script>
</body>
</html>