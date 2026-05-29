<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode([]);
    exit();
}

$customer_id = $_SESSION['user_id'];
$sql = "SELECT id, order_status FROM orders WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$statuses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $statuses[$row['id']] = $row['order_status'];
}
echo json_encode($statuses);
?>