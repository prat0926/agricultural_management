<?php
// index.php
require_once 'config/database.php';;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmFresh - Agriculture Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="farmer_login.php" class="btn-farmer">Farmer Login</a></li>
                <li><a href="customer_login.php" class="btn-customer">Customer Login</a></li>
            </ul>
        </div>
    </nav>

    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Fresh from Farm <br>to Your Table</h1>
                <p>Directly connect with local farmers and get the freshest vegetables at the best prices. No middlemen, no compromise on quality.</p>
                <div class="hero-buttons">
                    <a href="customer_login.php" class="btn-primary">Shop Now</a>
                    <a href="farmer_login.php" class="btn-secondary">Start Selling</a>
                </div>
            </div>
            <div class="hero-image">
                <i class="fas fa-tractor"></i>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <h2>Why Choose FarmFresh?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-seedling"></i>
                    <h3>100% Fresh</h3>
                    <p>Directly sourced from farms, delivered within 24 hours</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-rupee-sign"></i>
                    <h3>Best Prices</h3>
                    <p>No middlemen means better prices for everyone</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-truck"></i>
                    <h3>Fast Delivery</h3>
                    <p>Free same-day delivery on orders above ₹500</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Expiry Tracking</h3>
                    <p>Real-time expiry date tracking for all products</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 FarmFresh - Agriculture Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>