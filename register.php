<?php
// register.php
include 'config/database.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    
    $check_sql = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username or email already exists!";
    } else {
        $sql = "INSERT INTO users (username, email, password, phone, address, user_type) 
                VALUES ('$username', '$email', '$password', '$phone', '$address', '$user_type')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully! You can now login.";
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - FarmFresh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Create New Account</h2>
                <p>Join FarmFresh today</p>
            </div>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea name="address" placeholder="Full Address" rows="3" required></textarea>
                </div>
                <div class="input-group">
                    <i class="fas fa-user-tag"></i>
                    <select name="user_type" required>
                        <option value="">Select Account Type</option>
                        <option value="customer">Customer - I want to buy products</option>
                        <option value="farmer">Farmer - I want to sell products</option>
                    </select>
                </div>
                <button type="submit" class="register-btn">Create Account</button>
            </form>
            <div class="register-footer">
                <p>Already have an account? <a href="#" onclick="redirectToLogin()">Login here</a></p>
                <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
    
    <script>
    function redirectToLogin() {
        const userType = document.querySelector('select[name="user_type"]').value;
        if(userType === 'farmer') {
            window.location.href = 'farmer_login.php';
        } else if(userType === 'customer') {
            window.location.href = 'customer_login.php';
        } else {
            alert('Please select account type first');
        }
    }
    </script>
</body>
</html>