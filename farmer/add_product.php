<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../farmer_login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $farmer_id = $_SESSION['user_id'];
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['product_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . uniqid() . '.' . $ext;
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_dir . $new_filename)) {
                $image_path = 'uploads/' . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, PNG, GIF files allowed.";
        }
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO products (farmer_id, product_name, category, price, quantity, unit, expiry_date, description, image) 
                VALUES ('$farmer_id', '$product_name', '$category', '$price', '$quantity', '$unit', '$expiry_date', '$description', '$image_path')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Product added successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - FarmFresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for form container (matches original design) */
        .form-container {
            max-width: 700px;
            margin: 100px auto 50px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(135deg, #2c5f2d, #1a3b1a);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .form-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .form-header h2 {
            margin: 0;
            font-size: 1.8rem;
        }
        .form-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }
        .form-body {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }
        .submit-btn {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.3);
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .form-container {
                margin: 80px 20px 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar (same as original) -->
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

    <!-- Form Container -->
    <div class="form-container">
        <div class="form-header">
            <i class="fas fa-plus-circle"></i>
            <h2>Add New Product</h2>
            <p>List your fresh produce for customers</p>
        </div>
        <div class="form-body">
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" required placeholder="e.g., Fresh Tomatoes">
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Grains">Grains</option>
                        <option value="Herbs">Herbs</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" name="price" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" required placeholder="Stock amount">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit">
                            <option value="kg">kg</option>
                            <option value="gram">gram</option>
                            <option value="piece">piece</option>
                            <option value="bunch">bunch</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe your product (e.g., organic, locally grown, etc.)"></textarea>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="product_image" accept="image/*">
                    <small style="color:#666;">Optional. Upload JPG, PNG or GIF.</small>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-cloud-upload-alt"></i> Add Product
                </button>
            </form>
        </div>
    </div>
</body>
</html>