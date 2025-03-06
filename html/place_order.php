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

// Fetch user's cart items and check stock availability
$productQuery = mysqli_query($con, "SELECT cart.productid, cart.quantity, products.stock_in, products.productname 
                                    FROM cart 
                                    JOIN products ON cart.productid = products.pid 
                                    WHERE cart.userid = '$userid'");

$insufficientStock = false;
$products = [];

while ($row = mysqli_fetch_assoc($productQuery)) {
    $productid = $row['productid'];
    $productname = $row['productname'];
    $quantity = $row['quantity'];
    $stock = $row['stock_in'];

    if ($quantity > $stock) {
        echo "insufficient_stock_$productname"; // Indicate which product is out of stock
        exit;
    }

    $products[] = [
        'productid' => $productid,
        'quantity' => $quantity
    ];
}

// If stock is sufficient for all products, proceed with placing the order
foreach ($products as $product) {
    $productid = $product['productid'];
    $quantity = $product['quantity'];

    // Insert order details into the orders table
    mysqli_query($con, "INSERT INTO orders (userid, productid, quantity) VALUES ('$userid', '$productid', '$quantity')");

    // Deduct the stock after successful order placement
    mysqli_query($con, "UPDATE products SET stock_in = stock_in - $quantity WHERE pid = '$productid'");
}

// Clear the cart after placing the order
mysqli_query($con, "DELETE FROM cart WHERE userid = '$userid'");

echo "order_placed";
