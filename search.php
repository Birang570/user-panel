<?php
require_once "include/db_connect.php";

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($con, $_GET['query']);

    // Search for products, categories, and subcategories
    $query = "SELECT productname AS name, 'product' AS type FROM products WHERE productname LIKE '%$search%'
              UNION
              SELECT category AS name, 'category' AS type FROM category WHERE category LIKE '%$search%'
              UNION
              SELECT subcategory AS name, 'subcategory' AS type FROM subcategory WHERE subcategory LIKE '%$search%'";

    $result = mysqli_query($con, $query);

    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = [
            'name' => $row['name'],
            'type' => $row['type']
        ];
    }

    echo json_encode($suggestions);
}
