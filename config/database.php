<?php
// config/database.php

$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'agriculture_db';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// The rest of your existing functions (isLoggedIn, isFarmer, etc.) should remain here exactly as they were.
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
if (!function_exists('isFarmer')) {
    function isFarmer() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'farmer';
    }
}
if (!function_exists('isCustomer')) {
    function isCustomer() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer';
    }
}
if (!function_exists('redirectIfNotLoggedIn')) {
    function redirectIfNotLoggedIn() {
        if (!isLoggedIn()) {
            header("Location: ../login.php");
            exit();
        }
    }
}
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
    }
}
?>