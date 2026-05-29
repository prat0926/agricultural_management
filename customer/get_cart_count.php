<?php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode(['count' => 0]);
    exit();
}

$customer_id = $_SESSION['user_id'];
$sql = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo json_encode(['count' => $row['total'] ? $row['total'] : 0]);
?><?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode(['count' => 0]);
    exit();
}

$customer_id = $_SESSION['user_id'];
$sql = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
echo json_encode(['count' => $row['total'] ? (int)$row['total'] : 0]);
?><?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    echo json_encode(['count' => 0]);
    exit();
}

$customer_id = $_SESSION['user_id'];
$sql = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
echo json_encode(['count' => $row['total'] ? (int)$row['total'] : 0]);
?>