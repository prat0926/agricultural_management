<?php
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../farmer_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $farmer_id = $_SESSION['user_id'];
    
    $sql = "DELETE FROM products WHERE id = $product_id AND farmer_id = $farmer_id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?msg=deleted");
    } else {
        header("Location: dashboard.php?msg=error");
    }
} else {
    header("Location: dashboard.php");
}
exit();
?>