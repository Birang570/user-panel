<?php
require_once "../include/db_connect.php";
$isPassMatch = true;
$isPass = true;
if (isset($_POST['sign'])) {
    $username1 = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password1 = $_POST['password'];
    $password2 = $_POST['confirmPassword'];

    if(preg_match("/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $password1)) 
    {
        if ($password1 == $password2) {
            $password1 = encryptData($password1);
            //checking if the user exists in the database
            $sql4 = "SELECT * FROM users WHERE contactno='$mobile'";
            $result = mysqli_query($con, $sql4);
            $count = mysqli_num_rows($result);

            if ($count == 0) {
                $sql5 = "INSERT INTO users(username, email, password, contactno) VALUES('$username1', '$email', '$password1', '$mobile')";
                $result1 = mysqli_query($con, $sql5);

                if ($result1) {
                    echo "Successfully signed up";
                }
            } else {
                $loginError = "This Phone No is already taken";
                header("Location: signup.php?error=" . urlencode($loginError));
                exit;
            }
            header("Location: login.php");
            exit;
        } else {
            $isPassMatch = false;
        }
    } else {
        $isPass = false;
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
        .error-message {
            margin-top: 5px;
            font-size: 0.9rem;
            color: red;
        }
    </style>
</head>

<body>

    <!-- Sign Up Form -->
    <div class="login-container">

        <h2>sign up</h2>
        <form id="signupForm" action="" method="post">
            <?php
            $display = 'transparent';
            $display1 = 'transparent';
            $display2 = 'none';
            if (isset($_GET['error'])) {
                $display = 'red';
            }
            if ($isPassMatch == false) {
                $display1 = 'red';
            }
            if ($isPass == false) {
                $display2 = 'block';
            }
            ?>
            <p align='center' id='warning' style='color: <?php echo $display; ?>;'>This Phone No is already Used</p>
            <p align='center' id='warning' style='color: <?php echo $display1; ?>;'>Password Is Not Match</p>
            <!-- Username Field -->
            <div class="input-group">
                <label for="username"></label>
                <input type="text" id="username" name="name" placeholder="Enter username" minlength="2" title="Enter username with minimum 2 character" pattern="[A-Za-z0-9]+" required>
            </div>

            <!-- Email Field -->
            <div class="input-group">
                <label for="email"></label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <!-- Mobile Field -->
            <div class="input-group">
                <label for="mobile"></label>
                <input type="tel" id="mobile" name="mobile" minlength="10" maxlength="10" title="Enter your 10 Digit mobile number" placeholder="Enter your mobile number" required>
            </div>

            <!-- Password Field -->
            <div class="input-group">
                <label for="password"></label>
                <input type="password" id="password" minlength="8" maxlength="16" title="Enter your Password with minimum 8 character long only allowed charater, digit, 1 uppercase" pattern="[A-Za-z0-9]+" name="password" placeholder="Enter your password" required>
                <p align='center' id='warning' style='color: red; display: <?php echo $display2; ?>;'>Password must be minimum 8 character long only allowed charater, digit, 1 uppercase</p>
            </div>

            <!-- Confirm Password Field -->
            <div class="input-group">
                <label for="confirmPassword"></label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn" name="sign" style="background-color: #006c8b;">Sign Up</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

</body>

</html>