<?php
require_once "include/db_connect.php";

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($con, $_GET['query']);

    // Search in products
    $productQuery = "SELECT * FROM products WHERE productname LIKE '%$search%'";
    $productResult = mysqli_query($con, $productQuery);

    // Search in categories
    $categoryQuery = "SELECT * FROM category WHERE category LIKE '%$search%'";
    $categoryResult = mysqli_query($con, $categoryQuery);

    echo "<h3>Search Results for '$search'</h3>";

    if (mysqli_num_rows($productResult) > 0) {
        echo "<h4>Products:</h4><div class='product-list'>";
        while ($product = mysqli_fetch_assoc($productResult)) {
            echo "<div class='product'>
                    <img src='../admin/{$product['productimage1']}' alt='{$product['productname']}'>
                    <h4>{$product['productname']}</h4>
                    <p>₹{$product['productprice']}</p>
                    <button class='add-to-cart' data-name='{$product['productname']}' data-price='{$product['productprice']}'>Add to Cart</button>
                  </div>";
        }
        echo "</div>";
    }

    if (mysqli_num_rows($categoryResult) > 0) {
        echo "<h4>Categories:</h4><div class='category-list'>";
        while ($category = mysqli_fetch_assoc($categoryResult)) {
            echo "<a href='?category_id={$category['cid']}' class='category-item'>{$category['category']}</a>";
        }
        echo "</div>";
    }

    if (mysqli_num_rows($productResult) == 0 && mysqli_num_rows($categoryResult) == 0) {
        echo "<p>No results found.</p>";
    }
}
?>
