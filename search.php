<?php
require_once "include/db_connect.php";

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($con, $_GET['query']);

    // Search in products and categories
    $query = "SELECT productname AS name FROM products WHERE productname LIKE '%$search%'
              UNION
              SELECT category AS name FROM category WHERE category LIKE '%$search%'";

    $result = mysqli_query($con, $query);

    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row['name'];
    }

    echo json_encode($suggestions);
}
?>
