<?php
session_start();
require_once "include/db_connect.php";

if (isset($_POST['category_id']) && is_numeric($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    $query = "SELECT * FROM products WHERE categoryname = $category_id LIMIT 10";
} else {
    echo "<h3>No category selected.</h3>";
    exit;
}

$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='products'>";
    while ($product = mysqli_fetch_assoc($result)) {
        echo "<div class='product'>";
        echo "<div class='image-container'>
        <img class='product-image' src='../admin/{$product['productimage1']}' alt='{$product['productname']}'>
        </div>";
        echo "<h4>{$product['productname']}</h4>";
        echo "<p>₹{$product['productprice']}</p>";
        if (isset($_SESSION['username'])) {
            if ($product['stock_in'] > 0) {
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='productid' value='{$product['pid']}'>";
                echo "<button class='add-to-cart' name='addCart'>Add to Cart</button>";
                echo "</form>";
            } else {
                echo "<button class='add-to-cart' style='cursor: not-allowed; background-color: #0098c7;' disabled>Out Of Stock</button>";
            }
        } else {
            echo "<button class='add-to-cart' disabled>Add to Cart</button>";
        }
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<h3>No products found in this category.</h3>";
}
