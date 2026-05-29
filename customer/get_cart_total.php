<?php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode(['total' => 0]);
    exit();
}

$customer_id = $_SESSION['user_id'];
$sql = "SELECT SUM(p.price * c.quantity) as total 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo json_encode(['total' => $row['total'] ? $row['total'] : 0]);
?>