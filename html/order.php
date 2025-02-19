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
if (isset($_POST['addCart'])) {
    $productid = $_POST['productid'];
    $userid = $_SESSION['uid'];

    $cartQuery = mysqli_query($con, "SELECT * FROM cart WHERE userid = $userid AND productid = $productid");
    if (mysqli_num_rows($cartQuery) > 0) {
        $cart = mysqli_fetch_assoc($cartQuery);
        // $quantity = $cart['quantity'] + 1;
        mysqli_query($con, "UPDATE cart SET quantity = quantity+1 WHERE userid = $userid AND productid = $productid");
    } else {
        mysqli_query($con, "INSERT INTO cart (userid, productid) VALUES ($userid, $productid)");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 26px;
        }

        .order-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .cart-button {
            padding: 10px 20px;
            background-color: #ff6347;
            /* Tomato color */
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }

        .cart-button:hover {
            background-color: #e5533d;
            /* Darker tomato */
        }

        .order-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .order-info img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            margin-right: 15px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .order-details {
            margin-top: 10px;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: none;
            width: calc(100% - 30px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .order-status {
            font-weight: bold;
        }

        .delivered {
            color: green;
        }

        .shipped {
            color: orange;
        }

        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            font-weight: bold;
        }

        button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        header {
            background-color: #fff;
            display: flex;
            height: 40px;
            padding: 10px;
            align-content: center;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <header>
        <h2 style="padding: 0; margin: 0;"><?php echo $storeName; ?></h2>
        <a class="cart-button" href="cart.php" id="cart-button">Cart</a>
    </header>
    <div class="container">
        <div id="orderList">
            <?php
            $order = mysqli_query($con, "SELECT users.username, products.*, orders.* FROM orders 
    JOIN users ON users.uid = orders.userId
    JOIN products ON products.pid = orders.productId
    WHERE username = '$username' group by products.pid, orders.orderDate order by orders.orderDate desc");
            if (mysqli_num_rows($order) > 0) {
                while ($row = mysqli_fetch_array($order)) {
            ?>
                    <div class="order-item">
                        <div class="order-info">
                            <img src="../../admin/<?php echo $row['productimage1'] ?>" alt="<?php echo $row['productname'] ?>">
                            <div>
                                <strong>Order #<?php echo $row['oid'] ?></strong><br>
                                <span><?php echo $row['productname'] ?></span><br>
                                <span class="order-status delivered"><?php echo $row['orderStatus'] ?></span>
                            </div>
                            <button onclick="toggleOrderDetails(this)">View Details</button>
                        </div>
                        <div class="order-details">
                            <p><strong>Order ID:</strong> <?php echo $row['oid'] ?></p>
                            <p><strong>Product:</strong> <?php echo $row['productname'] ?></p>
                            <p><strong>Quantity:</strong> <?php echo $row['quantity'] ?></p>
                            <p><strong>Total Price:</strong> ₹<?php echo $row['quantity'] * $row['productprice'] ?></p>
                            <p><strong>Status:</strong> <?php echo $row['orderStatus'] ?></p>
                            <form action="" method="post">
                                <input type="text" name="productid" value="<?php echo $row['pid'] ?>" hidden>
                                <button class="add-to-cart" name="addCart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No orders found.</p>";
            }
            ?>
        </div>

    </div>

    <script>
        function toggleOrderDetails(button) {
            let details = button.parentElement.nextElementSibling;
            if (details.style.display === "none" || details.style.display === "") {
                details.style.display = "block";
                button.textContent = "Hide Details";
            } else {
                details.style.display = "none";
                button.textContent = "View Details";
            }
        }
    </script>
</body>

</html>