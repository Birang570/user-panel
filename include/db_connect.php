<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "store";

    $con = mysqli_connect($host,$user,$password,$db);

    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
?>
