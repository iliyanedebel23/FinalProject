<?php

require_once "../backend/config.php";
require_once "session.php";

$error = ''; // Initialize error message variable
$success = ''; // Initialize success message variable

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $fullname = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST["confirm_password"]);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Check if email is already registered
    if ($query = $db->prepare("SELECT * FROM users WHERE email = ?")) {
        $query->bind_param('s', $email);
        $query->execute();
        $query->store_result();
        
        if ($query->num_rows > 0) {
            $error .= '<p class="error">The email address is already registered!</p>';
        } else {
            if (strlen($password) < 6) {
                $error .= '<p class="error">Password must have at least 6 characters.</p>';
            }

            if (empty($confirm_password)) {
                $error .= '<p class="error">Please enter confirm password.</p>';
            } elseif ($password !== $confirm_password) {
                $error .= '<p class="error">Passwords did not match.</p>';
            }

            if (empty($error)) {
                $insertQuery = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?);");
                $insertQuery->bind_param("sss", $fullname, $email, $password_hash);
                if ($insertQuery->execute()) {
                    // Store user session and redirect to index.php
                    session_start();
                    $_SESSION['username'] = $fullname;
                    header("Location: index.php");
                    exit();
                } else {
                    $error .= '<p class="error">Something went wrong during registration!</p>';
                }
                $insertQuery->close();
            }
        }
        $query->close();
    } else {
        $error .= '<p class="error">Database query error!</p>';
    }

    // Close DB connection
    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nintendo E-Shop</title>
    <link rel="stylesheet" href="style.css"> <!-- Use the main site stylesheet -->
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Nintendo E-Shop</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="store.php">Products</a></li>
            </ul>
        </nav>
    </div>
</header>

<section id="register-section" style="padding: 60px 0;">
    <div class="container" style="max-width: 400px; margin: 0 auto;">
        <h2 style="text-align: center;">Register</h2>
        <p style="text-align: center;">Please fill this form to create an account.</p>
        <?php echo $success; ?>
        <?php echo $error; ?>
        <form action="" method="post" style="background-color: #0b1b2b; padding: 30px; border-radius: 10px;">
            <div class="form-group">
                <label for="name" style="color: #e0e0e0;">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>    
            <div class="form-group">
                <label for="email" style="color: #e0e0e0;">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>    
            <div class="form-group">
                <label for="password" style="color: #e0e0e0;">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password" style="color: #e0e0e0;">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" class="add-to-cart btn" value="Submit" style="background-color: #d4af37; width: 100%;">
            </div>
            <p style="color: #e0e0e0; text-align: center;">Already have an account? <a href="login.php" style="color: #d4af37;">Login here</a>.</p>
        </form>
    </div>
</section>

<footer style="background-color: #0b1b2b; color: #e0e0e0; text-align: center; padding: 20px;">
    <p>&copy; 2025 Nintendo E-Shop. All rights reserved.</p>
</footer>

</body>
        
        <style>
        .form-group {
            margin-bottom: 20px;
                margin: auto;
  width: 50%;
  padding: 10px;
                
        }
                
        
        </style>
</html>
