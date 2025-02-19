<?php
session_start();
require_once "../include/db_connect.php";

if (isset($_SESSION["uid"])) {
    $userid = $_SESSION['uid'];
} else {
    header("Location: login.php");
    exit;
}

// Fetch user shipping address
$addressQuery = mysqli_query($con, "SELECT shippingaddress FROM users WHERE uid = '$userid'");
$address1 = mysqli_fetch_assoc($addressQuery);

// Fetch cart products
$productQuery = mysqli_query($con, "SELECT cart.productid, cart.quantity, products.productname, products.productprice, products.productimage1 
                                    FROM cart
                                    JOIN products ON cart.productid = products.pid 
                                    WHERE cart.userid = '$userid'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === "update_quantity" && isset($_POST['productid']) && isset($_POST['quantity'])) {
            $productid = $_POST['productid'];
            $quantity = $_POST['quantity'];

            mysqli_query($con, "UPDATE cart SET quantity = '$quantity' WHERE userid = '$userid' AND productid = '$productid'");
            echo "quantity_updated";
            exit;
        }

        if ($action === "remove_item" && isset($_POST['productid'])) {
            $productid = $_POST['productid'];

            mysqli_query($con, "DELETE FROM cart WHERE userid = '$userid' AND productid = '$productid'");
            echo "item_removed";
            exit;
        }

        if ($action === "update_address" && isset($_POST['address'])) {
            $newAddress = mysqli_real_escape_string($con, $_POST['address']);

            $updateQuery = "UPDATE users SET shippingaddress = '$newAddress' WHERE uid = '$userid'";
            if (mysqli_query($con, $updateQuery)) {
                echo "address_updated";
            } else {
                echo "address_update_failed";
            }
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../css/cart.css">
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo"><?php echo $storeName; ?></div>
            <button class="cart-btn cart-button" id="cart-button">Cart <span id="cart-count">(0)</span></button>
        </nav>
    </header>

    <div class="cart-container">
        <div class="cart-items">
            <h2>Shopping Cart</h2>
            <div id="cart-list">
                <?php while ($row = mysqli_fetch_assoc($productQuery)) { ?>
                    <div class="cart-item" data-id="<?php echo $row['productid']; ?>">
                        <img src="../../admin/<?php echo $row['productimage1']; ?>" alt="Product Image">
                        <div class="cart-item-details">
                            <h3><?php echo $row['productname']; ?></h3>
                            <p class="price">₹<?php echo $row['productprice']; ?></p>
                            <div class="flex">
                                <div class="quantity">
                                    <button class="qty-btn minus" data-id="<?php echo $row['productid']; ?>">-</button>
                                    <span class="quantity-value"><?php echo $row['quantity']; ?></span>
                                    <button class="qty-btn plus" data-id="<?php echo $row['productid']; ?>">+</button>
                                </div>
                                <button class="remove-btn" data-id="<?php echo $row['productid']; ?>">Remove</button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="price-details">
            <h2>PRICE DETAILS</h2>
            <p>Price (<span id="item-count">0</span> items): <span class="price-value">₹0</span></p>
            <h3>Total Amount: <span class="total-value">₹0</span></h3>

            <div id="address-section">
                <h3>Delivery Address</h3>
                <div class="address-container">
                    <textarea id="address" name="address" disabled><?php echo $address1['shippingaddress']; ?></textarea>
                    <button type="button" id="edit-address-btn" onclick="editAddress()">Edit</button>
                </div>
            </div>

            <button class="place-order-btn">PLACE ORDER</button>
        </div>
    </div>

</body>
<script>
    function editAddress() {
        let addressField = document.getElementById("address");
        let editBtn = document.getElementById("edit-address-btn");

        if (addressField.disabled) {
            addressField.disabled = false;
            editBtn.innerText = "Save";
        } else {
            let newAddress = addressField.value;
            fetch("cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=update_address&address=${encodeURIComponent(newAddress)}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "address_updated") {
                        addressField.disabled = true;
                        editBtn.innerText = "Edit";
                        alert("Address updated successfully!");
                    } else {
                        alert("Failed to update address.");
                    }
                });
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        function updateCart() {
            let itemCount = 0;
            let totalPrice = 0;

            document.querySelectorAll(".cart-item").forEach(item => {
                let quantity = parseInt(item.querySelector(".quantity-value").textContent);
                let price = parseInt(item.querySelector(".price").textContent.replace("₹", ""));
                itemCount += quantity;
                totalPrice += price * quantity;
            });

            document.getElementById("item-count").textContent = itemCount;
            document.querySelector(".price-value").textContent = "₹" + totalPrice;
            document.querySelector(".total-value").textContent = "₹" + totalPrice;
        }

        document.querySelectorAll(".qty-btn.plus").forEach(button => {
            button.addEventListener("click", function() {
                let productId = this.getAttribute("data-id");
                let quantitySpan = this.previousElementSibling;
                let newQuantity = parseInt(quantitySpan.textContent) + 1;
                quantitySpan.textContent = newQuantity;

                fetch("cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=update_quantity&productid=${productId}&quantity=${newQuantity}`
                }).then(() => updateCart());
            });
        });

        document.querySelectorAll(".qty-btn.minus").forEach(button => {
            button.addEventListener("click", function() {
                let productId = this.getAttribute("data-id");
                let quantitySpan = this.nextElementSibling;
                let newQuantity = Math.max(1, parseInt(quantitySpan.textContent) - 1);
                quantitySpan.textContent = newQuantity;

                fetch("cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=update_quantity&productid=${productId}&quantity=${newQuantity}`
                }).then(() => updateCart());
            });
        });

        document.querySelectorAll(".remove-btn").forEach(button => {
            button.addEventListener("click", function() {
                let productId = this.getAttribute("data-id");
                let cartItem = this.closest(".cart-item");
                cartItem.remove();

                fetch("cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=remove_item&productid=${productId}`
                }).then(() => updateCart());
            });
        });

        updateCart();
    });
    document.querySelector(".place-order-btn").addEventListener("click", function() {
        fetch("place_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                }
            })
            .then(response => response.text())
            .then(data => {
                if (data === "order_placed") {
                    alert("Order placed successfully!");
                    location.reload(); // Reload to clear cart UI
                } else if (data === "cart_empty") {
                    alert("Your cart is empty!");
                } else if (data === "order_amount_too_low") {
                    alert("Your total cart amount is below the minimum order amount!");
                } else {
                    alert("Failed to place order. Try again.");
                }
            });
    });
</script>

</html>