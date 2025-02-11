<?php
require_once "../include/db_connect.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f3f6;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #2874f0;
            padding: 15px;
            color: white;
        }

        .cart-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .cart-items {
            width: 64%;
            background: white;
            padding: 20px;
            border-radius: 5px;
        }

        .cart-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item img {
            width: 200px;
            /* Set a fixed width */
            height: 200px;
            /* Set a fixed height */
            object-fit: contain;
            /* Ensures the entire image fits without distortion */
            border-radius: 5px;
            background-color: white;
            /* Optional: Adds a background to prevent transparency issues */
        }


        .cart-item-details {
            margin-left: 30px;
            width: 170px;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: green;
        }

        .original {
            text-decoration: line-through;
            color: gray;
            font-size: 14px;
        }

        .discount {
            color: red;
            font-size: 14px;
        }

        .quantity {
            margin-top: 10px;
        }

        .qty-btn {
            background: #ddd;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .remove-btn {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            margin-top: 10px;
            cursor: pointer;
        }

        .price-details {
            width: 29%;
            background: white;
            padding: 20px;
            border-radius: 5px;
        }

        .place-order-btn {
            background: orange;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 18px;
            cursor: pointer;
            color: white;
        }

        .flex {
            display: flex;
            justify-content: space-between;
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
        }

        .cart-button:hover {
            background-color: #e5533d;
            /* Darker tomato */
        }

        .cart-button #cart-count {
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo">Flipkart <span>Explore Plus</span></div>
            <button class="cart-btn cart-button" id="cart-button">Cart <span id="cart-count">(0)</span></button>
        </nav>
    </header>

    <div class="cart-container">
        <div class="cart-items">
            <h2>Shopping Cart</h2>
            <div id="cart-list"></div>
        </div>

        <div class="price-details">
            <h2>PRICE DETAILS</h2>
            <p>Price (<span id="item-count">0</span> items): <span class="price-value">₹0</span></p>
            <h3>Total Amount: <span class="total-value">₹0</span></h3>
            <button class="place-order-btn">PLACE ORDER</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cartList = document.getElementById("cart-list");
            const totalValue = document.querySelector(".total-value");
            const priceValue = document.querySelector(".price-value");
            const itemCount = document.getElementById("item-count");
            const cartCount = document.getElementById("cart-count");

            function getCart() {
                return JSON.parse(localStorage.getItem("cart")) || [];
            }

            function saveCart(cart) {
                localStorage.setItem("cart", JSON.stringify(cart));
            }

            function updateCartUI() {
                let cart = getCart();
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

                priceValue.textContent = `₹${total}`;
                totalValue.textContent = `₹${total}`;
                itemCount.textContent = count;
                cartCount.textContent = `(${count})`;

                addEventListeners();
            }

            function addEventListeners() {
                document.querySelectorAll(".qty-btn.plus").forEach(button => {
                    button.addEventListener("click", function() {
                        let cart = getCart();
                        cart[this.dataset.index].quantity++;
                        saveCart(cart);
                        updateCartUI();
                    });
                });

                document.querySelectorAll(".qty-btn.minus").forEach(button => {
                    button.addEventListener("click", function() {
                        let cart = getCart();
                        if (cart[this.dataset.index].quantity > 1) {
                            cart[this.dataset.index].quantity--;
                        } else {
                            cart.splice(this.dataset.index, 1);
                        }
                        saveCart(cart);
                        updateCartUI();
                    });
                });

                document.querySelectorAll(".remove-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        let cart = getCart();
                        cart.splice(this.dataset.index, 1);
                        saveCart(cart);
                        updateCartUI();
                    });
                });
            }

            updateCartUI();
        });
    </script>

</body>

</html>