<?php
require_once "include/db_connect.php";

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($con, $_GET['query']);

    // Search for products that match by name, category, or subcategory
    $productQuery = "
        SELECT DISTINCT p.* 
        FROM products p
        LEFT JOIN category c ON p.categoryname = c.cid
        LEFT JOIN subcategory s ON p.subcategoryname = s.sid
        WHERE p.productname LIKE '%$search%' 
        OR c.category LIKE '%$search%'
        OR s.subcategory LIKE '%$search%'
    ";

    $productResult = mysqli_query($con, $productQuery);

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
    } else {
        echo "<p>No results found.</p>";
    }
}
?>
