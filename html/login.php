<?php
session_start();
require_once "../include/db_connect.php";

// Check if the form is submitted for login
if (isset($_POST['login'])) {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Implement proper password hashing and validation using prepared statements
    $sql = "SELECT username,uid FROM users WHERE contactno = ? AND password = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $phone, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $username,$uid);
        mysqli_stmt_fetch($stmt);

        // $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['uid'] = $uid;

        // ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // Set a longer session expiration time (e.g., 30 days)

        header("Location: ../index.php");
        exit;
    } else {
        $loginError = "Invalid username or password";
        header("Location: login.php?error=" . urlencode($loginError));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | SoundPlus</title>
    <link rel="stylesheet" href="../css/creditianls.css">
    <style>
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Login Form -->
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <?php
            if (isset($_GET['error'])) {
                $loginError = $_GET['error'];
                echo "<p align='center' id='warning'>$loginError</p>";
            }
            ?>
            <!-- Identifier (Email or Mobile) -->
            <div class="input-group">
                <label for="identifier"></label>
                <input type="text" id="identifier" name="phone" placeholder="Enter mobile Number" required>
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <!-- Buttons -->
            <button type="submit" name="login" style="background-color: #006c8b;" class="btn">Login</button>
            <button type="button" class="forgot-btn">Forgot Password?</button>

            <!-- Signup Link -->
            <div class="signup-link">
                Don’t have an account? <a href="signup.php">Sign Up</a>
            </div>
        </form>
    </div>

</body>

</html>