<?php
// Enable error reporting for debugging (remove after fixing)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../vendor/autoload.php';

\Cloudinary::config([
    'cloud_name' => 'YOUR_CLOUD_NAME',
    'api_key' => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET',
]);

// Then inside the POST handling:
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
    $upload = \Cloudinary\Uploader::upload($_FILES['product_image']['tmp_name']);
    $image_url = $upload['secure_url']; // store this in your database
} // use require_once

header('Content-Type: application/json');

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$customer_id = (int)$_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

// Validate product
$query = "SELECT quantity, expiry_date, status FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit();
}
$product = mysqli_fetch_assoc($result);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}
if ($product['status'] != 'available') {
    echo json_encode(['success' => false, 'message' => 'Product is not available']);
    exit();
}
if ($product['quantity'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Out of stock']);
    exit();
}
if (strtotime($product['expiry_date']) < time()) {
    echo json_encode(['success' => false, 'message' => 'Product expired']);
    exit();
}

// Check cart
$check = "SELECT id, quantity FROM cart WHERE customer_id = $customer_id AND product_id = $product_id";
$chk_res = mysqli_query($conn, $check);
if (mysqli_num_rows($chk_res) > 0) {
    $row = mysqli_fetch_assoc($chk_res);
    $new_qty = $row['quantity'] + 1;
    if ($new_qty > $product['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Cannot exceed stock']);
        exit();
    }
    $update = "UPDATE cart SET quantity = $new_qty WHERE id = {$row['id']}";
    if (mysqli_query($conn, $update)) {
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} else {
    $insert = "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, 1)";
    if (mysqli_query($conn, $insert)) {
        echo json_encode(['success' => true, 'message' => 'Added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Insert failed: ' . mysqli_error($conn)]);
    }
}
?>