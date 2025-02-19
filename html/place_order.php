<?php
session_start();
require_once "../include/db_connect.php";

$userid = $_SESSION['uid'];

// Fetch minimum order amount from store
$orderamountQuery = mysqli_query($con, "SELECT min_order_amount FROM store LIMIT 1");
$orderamount = mysqli_fetch_assoc($orderamountQuery)['min_order_amount'];

// Calculate total cart amount for the user
$totalAmountQuery = mysqli_query($con, "SELECT SUM(cart.quantity * products.productprice) AS total 
                                        FROM cart 
                                        JOIN products ON cart.productid = products.pid 
                                        WHERE cart.userid = '$userid'");
$totalAmount = mysqli_fetch_assoc($totalAmountQuery)['total'];

if ($totalAmount < $orderamount) {
    echo "order_amount_too_low"; // Return error if total amount is less than required
    exit;
}

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
    
    mysqli_query($con, "update products set stock_in=stock_in-$quantity WHERE pid = '$productid'");

    echo "order_placed";
} else {
    echo "cart_empty";
}
?>
