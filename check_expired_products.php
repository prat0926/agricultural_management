<?php
include 'config/database.php';

$current_date = date('Y-m-d');
$sql = "UPDATE products SET status = 'out_of_stock' WHERE expiry_date < '$current_date'";
mysqli_query($conn, $sql);

// Get count of expired products
$count_sql = "SELECT COUNT(*) as count FROM products WHERE expiry_date < '$current_date'";
$result = mysqli_query($conn, $count_sql);
$row = mysqli_fetch_assoc($result);

echo json_encode(['expired_count' => $row['count']]);
?>