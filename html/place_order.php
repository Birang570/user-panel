<?php
session_start();
require_once "../include/db_connect.php";

$userid = $_SESSION['uid'];

// Fetch user's cart items
$productQuery = mysqli_query($con, "SELECT productid, quantity FROM cart WHERE userid = '$userid'");

if (mysqli_num_rows($productQuery) > 0) {
    while ($row = mysqli_fetch_assoc($productQuery)) {
        $productid = $row['productid'];
        $quantity = $row['quantity'];

        // Insert order details into the orders table
        mysqli_query($con, "INSERT INTO orders (userid, productid, quantity) VALUES ('$userid', '$productid', '$quantity')");
    }

    // Clear the cart after placing the order
    mysqli_query($con, "DELETE FROM cart WHERE userid = '$userid'");

    echo "order_placed";
} else {
    echo "cart_empty";
}
?>
