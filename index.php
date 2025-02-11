<?php
require_once "include/db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="header">
        <div class="logo">My Grocery Store</div>
        <!-- <input type="search" id="search-bar" placeholder="Search for Grocery" class="search-bar">
        <div id="suggestions"></div> -->
        
        <div class="search-container">
            <input type="search" id="search-bar" placeholder="Search for Grocery" class="search-bar">
            <div id="suggestions"></div> 
        </div>

        <div class="menu">
            <div class="dropdown">
                <button class="dropdown-btn">User</button>
                <div class="dropdown-menu">
                    <a href="html/profile.html">My Profile</a>
                    <a href="#">Orders</a>
                    <a href="#">Logout</a>
                </div>
            </div>
            <a class="cart-button" href="html/cart.php" id="cart-button">Cart</a>
        </div>
    </header>
    <div class="main">
        <div class="slider">
            <div class="slides" id="slides">
                <div class="slide"><img class="" src="../admin/img/product/black walkswagon.png" alt="Slide 1"></div>
                <div class="slide"><img src="../admin/img/product/bob car.png" alt="Slide 2"></div>
                <div class="slide"><img src="../admin/img/product/oggy car.png" alt="Slide 3"></div>
                <div class="slide"><img src="../admin/img/product/red Walkswagon.png" alt="Slide 4"></div>
                <div class="slide"><img src="../admin/img/product/short gun.png" alt="Slide 5"></div>
                <div class="slide"><img src="../admin/img/product/top.png" alt="Slide 6"></div>
                <div class="slide"><img src="../admin/img/product/white walkswalgon.png" alt="Slide 7"></div>
            </div>
            <!-- Buttons for navigation -->
            <button class="slider-btn prev" onclick="prevSlide()">&#10094;</button>
            <button class="slider-btn next" onclick="nextSlide()">&#10095;</button>
        </div>

        <nav class="navbar">
            <?php
            $categoryQuery = mysqli_query($con, "SELECT * FROM category LIMIT 6");
            while ($category = mysqli_fetch_assoc($categoryQuery)) {
            ?>
                <a href="?category_id=<?php echo $category['cid']; ?>" class="cateimg" style="background-image: url('../admin/<?php echo $category['imageurl']; ?>');">
                    <?php echo $category['category']; ?>
                </a>
            <?php } ?>
        </nav>


        <section class="products">
            <h3>Best of Grocery</h3>
            <div class="product-list" id="product-list">
                <?php
                $categoryFilter = "";
                if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
                    $category_id = intval($_GET['category_id']);
                    $categoryFilter = "WHERE categoryname = $category_id";
                }

                $productQuery = mysqli_query($con, "SELECT * FROM products $categoryFilter LIMIT 10");
                while ($product = mysqli_fetch_assoc($productQuery)) {
                ?>
                    <div class="product">
                        <img src="../admin/<?php echo $product['productimage1'] ?>" alt="<?php echo $product['productname'] ?>">
                        <h4><?php echo $product['productname'] ?></h4>
                        <p>₹<?php echo $product['productprice'] ?></p>
                        <button class="add-to-cart" data-name="<?php echo $product['productname'] ?>" data-price="<?php echo $product['productprice'] ?>">Add to Cart</button>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
    <footer class="footer">
        <h2>THANK YOU FOR VISITING</h2>
        <p>© 2025 My Grocery Store</p>
    </footer>

    <script src="javascript/script.js"></script>
    <script>
        let index = 0;
        const slides = document.getElementById('slides');
        const totalSlides = document.querySelectorAll('.slide').length;

        function showSlide() {
            slides.style.transform = `translateX(-${index * 100}%)`;
        }

        function nextSlide() {
            index = (index + 1) % totalSlides;
            showSlide();
        }

        function prevSlide() {
            index = (index - 1 + totalSlides) % totalSlides;
            showSlide();
        }

        // Auto-slide every 3 seconds
        setInterval(nextSlide, 3000);

        // Cart functionality
        let cartCount = 0;
        // const cartCountSpan = document.getElementById('cart-count');
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', () => {
                cartCount++;
                // cartCountSpan.textContent = cartCount;
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const searchBar = document.getElementById("search-bar");
            const suggestionsDiv = document.getElementById("suggestions");

            searchBar.addEventListener("input", function() {
                let query = searchBar.value.trim();

                if (query.length === 0) {
                    suggestionsDiv.innerHTML = "";
                    return;
                }

                fetch(`search.php?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = "";
                        data.forEach(item => {
                            let suggestion = document.createElement("div");
                            suggestion.textContent = item;
                            suggestion.classList.add("suggestion-item");
                            suggestion.addEventListener("click", function() {
                                searchBar.value = item;
                                performSearch(item);
                                suggestionsDiv.innerHTML = "";
                            });
                            suggestionsDiv.appendChild(suggestion);
                        });
                    });
            });

            function performSearch(searchTerm) {
                fetch(`search_results.php?query=${searchTerm}`)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector(".main").innerHTML = html;
                    });
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            // const cartCountSpan = document.getElementById('cart-count');
            const addToCartButtons = document.querySelectorAll('.add-to-cart');

            function getCart() {
                return JSON.parse(localStorage.getItem("cart")) || [];
            }

            function saveCart(cart) {
                localStorage.setItem("cart", JSON.stringify(cart));
            }

            function updateCartCount() {
                const cart = getCart();
                // cartCountSpan.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
            }

            addToCartButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const name = button.getAttribute('data-name');
                    const price = parseFloat(button.getAttribute('data-price'));
                    const image = button.parentElement.querySelector("img").src;

                    let cart = getCart();
                    let existingProduct = cart.find(item => item.name === name);

                    if (existingProduct) {
                        existingProduct.quantity += 1;
                    } else {
                        cart.push({
                            name,
                            price,
                            quantity: 1,
                            image
                        });
                    }

                    saveCart(cart);
                    updateCartCount();
                });
            });

            updateCartCount();
        });
    </script>
</body>

</html>