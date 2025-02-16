<?php
session_start();
require_once "../include/db_connect.php";
if (isset($_SESSION['username'])) {
    $userLoggedIn = true;
    $username = $_SESSION['username'];
} else {
    $userLoggedIn = false;
    header("Location: login.php");
}
$profile = mysqli_query($con, "select * from users where username='$username'");
$row = mysqli_fetch_assoc($profile);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $update = mysqli_query($con, "update users set username='$name', email='$email', contactno='$mobile' where username='$username'");
    if ($update) {
        header("Location: ../index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #2874f0;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .edit-btn {
            float: right;
        }
    </style>
</head>

<body>

    <div class="container">
        <form action="" method="post">
            <h2>Profile Information</h2>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="firstName" value="<?php echo $row['username'] ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" value="<?php echo $row['email'] ?>">
            </div>
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile" id="mobile" value="<?php echo $row['contactno'] ?>">
            </div>
            <button id="saveBtn" name="submit">Save</button>
        </form>
    </div>
</body>

</html>