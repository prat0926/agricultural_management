<?php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../farmer_login.php");
    exit();
}

$product_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;
$farmer_id = $_SESSION['user_id'];

// Get product details
$sql = "SELECT * FROM products WHERE id = $product_id AND farmer_id = $farmer_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $update_sql = "UPDATE products SET 
                   product_name = '$product_name',
                   category = '$category',
                   price = '$price',
                   quantity = '$quantity',
                   unit = '$unit',
                   expiry_date = '$expiry_date',
                   description = '$description'
                   WHERE id = $product_id AND farmer_id = $farmer_id";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: dashboard.php?msg=updated");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - FarmFresh</title>
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
        <div class="form-container">
            <h2>Edit Product</h2>
            <form method="POST" action="" class="product-form">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Vegetables" <?php echo $product['category'] == 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                        <option value="Fruits" <?php echo $product['category'] == 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                        <option value="Grains" <?php echo $product['category'] == 'Grains' ? 'selected' : ''; ?>>Grains</option>
                        <option value="Herbs" <?php echo $product['category'] == 'Herbs' ? 'selected' : ''; ?>>Herbs</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit">
                            <option value="kg" <?php echo $product['unit'] == 'kg' ? 'selected' : ''; ?>>kg</option>
                            <option value="gram" <?php echo $product['unit'] == 'gram' ? 'selected' : ''; ?>>gram</option>
                            <option value="piece" <?php echo $product['unit'] == 'piece' ? 'selected' : ''; ?>>piece</option>
                            <option value="bunch" <?php echo $product['unit'] == 'bunch' ? 'selected' : ''; ?>>bunch</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" value="<?php echo $product['expiry_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Update Product</button>
            </form>
        </div>
    </div>

    <style>
    .form-container {
        max-width: 600px;
        margin: 100px auto 50px;
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .form-container h2 {
        margin-bottom: 1.5rem;
        color: #333;
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
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    .submit-btn {
        width: 100%;
        padding: 12px;
        background: #2196f3;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
    }
    .submit-btn:hover {
        background: #0b7dda;
    }
    </style>
</body>
</html>