<?php
session_start();
require_once "../include/db_connect.php";

$username = $_SESSION['username'];
if (!$username) {
    die("User not logged in");
}

// Fetch user's shipping address
$addressQuery = mysqli_query($con, "SELECT shippingaddress FROM users WHERE username = '$username'");
$address1 = mysqli_fetch_assoc($addressQuery);

// Handle order submission
if (isset($_POST["order"])) {
    $address2 = $_POST['address'];
    $products = $_POST['productname'];
    $quantities = $_POST['quantity'];

    echo "Address: " . htmlspecialchars($address2) . "<br>";
    echo "Products: " . htmlspecialchars($products) . "<br>";
    echo "Quantities: " . htmlspecialchars($quantities) . "<br>";
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
            <div class="logo">Flipkart <span>Explore Plus</span></div>
            <button class="cart-btn cart-button" id="cart-button">Cart <span id="cart-count">(0)</span></button>
        </nav>
    </header>

    <form action="" method="post">
        <div class="cart-container">
            <div class="cart-items">
                <h2>Shopping Cart</h2>
                <div id="cart-list"></div>
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

                <!-- Hidden input fields to store cart data -->
                <input type="hidden" name="productname" id="productname">
                <input type="hidden" name="quantity" id="quantity">

                <button class="place-order-btn" type="submit" name="order" id="place-order">PLACE ORDER</button>
            </div>
        </div>
    </form>

    <script>
        function editAddress() {
            var addressField = document.getElementById("address");
            var editBtn = document.getElementById("edit-address-btn");

            if (addressField.disabled) {
                addressField.disabled = false;
                addressField.style.background = "white";
                editBtn.innerText = "Save";
            } else {
                addressField.disabled = true;
                addressField.style.background = "#f9f9f9";
                editBtn.innerText = "Edit";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            console.log(localStorage.getItem("cart"));

            function getCart() {
                let cart = localStorage.getItem("cart");
                return cart ? JSON.parse(cart) : []; // Return empty array if null
            }

            function saveCart(cart) {
                localStorage.setItem("cart", JSON.stringify(cart));
            }

            function updateCartUI() {
                let cart = getCart();
                const cartList = document.getElementById("cart-list");
                const cartCount = document.getElementById("cart-count");
                const totalValue = document.querySelector(".total-value");

                if (!cartList || !cartCount || !totalValue) {
                    console.error("Cart elements not found in DOM");
                    return;
                }

                cartList.innerHTML = "";
                let total = 0;
                let count = 0;

                cart.forEach((item, index) => {
                    total += item.price * item.quantity;
                    count += item.quantity;

                    cartList.innerHTML += `
                    <div class="cart-item">
                        <img src="${item.image}" alt="Product Image">
                        <div class="cart-item-details">
                            <h3>${item.name}</h3>
                            <p class="price">₹${item.price}</p>
                            <div class="flex">
                                <div class="quantity">
                                    <button class="qty-btn minus" data-index="${index}">-</button>
                                    <span>${item.quantity}</span>
                                    <button class="qty-btn plus" data-index="${index}">+</button>
                                </div>
                                <button class="remove-btn" data-index="${index}">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
                });

                totalValue.textContent = `₹${total}`;
                cartCount.textContent = `(${count})`;
            }

            updateCartUI();
        });
    </script>

</body>

</html>