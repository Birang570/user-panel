<?php
session_start();

require_once "include/db_connect.php";

// Check if the form is submitted for logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
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
    <title>Grocery Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="header">
        <div class="logo"><?php echo $storeName; ?></div>

        <div class="search-container">
            <input type="search" id="search-bar" placeholder="Search for Grocery" class="search-bar">
            <div id="suggestions"></div>
        </div>

        <div class="menu">
            <div class="dropdown">
                <button class="dropdown-btn"><?PHP echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User' ?></button>
                <div class="dropdown-menu">
                    <a href="php/profile.php">My Profile</a>
                    <a href="php/order.php">Orders</a>
                    <form action="" method="post">
                        <?PHP echo isset($_SESSION['username']) ? '<button style="border: none; background: none;" type="submit" name="logout">Logout</button>' : '<a href="php/login.php" style="padding: 0;">Login</a>' ?>
                    </form>
                </div>
            </div>
            <a class="cart-button" href="php/cart.php" id="cart-button">Cart</a>
        </div>
    </header>
    <div class="main" id="main">
        <div class="slider">
            <div class="slides" id="slides">
                <?php
                $bannerQuery = mysqli_query($con, "SELECT * FROM banner WHERE bstatus = 1 LIMIT 7");
                while ($banner = mysqli_fetch_assoc($bannerQuery)) {
                ?>
                    <div class="slide"><img class="" src="../admin/<?php echo $banner['bannerimageurl']; ?>" alt="Slide"></div>
                <?php } ?>
            </div>
            <!-- Buttons for navigation -->
            <button class="slider-btn prev" onclick="prevSlide()">&#10094;</button>
            <button class="slider-btn next" onclick="nextSlide()">&#10095;</button>
        </div>

        <h3 style="padding:20px;">Suggested Category</h3>
        <nav class="navbar">
            <?php
            $categoryQuery = mysqli_query($con, "SELECT * FROM category WHERE cstatus = 1 LIMIT 6");
            while ($category = mysqli_fetch_assoc($categoryQuery)) {
            ?>
                <div style="background-color:#d4fff0; 
                            align-items: center;
                            align-content: center;
                            display: flex;
                            flex-direction: column;
                            border-radius:10px;
                            flex-wrap: wrap;">

                    <a href="javascript:void(0);" class="category-link cateimg"
                        style="background-image: url('../admin/<?php echo $category['imageurl']; ?>');"
                        onclick="loadCategory(<?php echo $category['cid']; ?>)">
                    </a>

                    <?php echo $category['category']; ?>
                </div>
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

                $productQuery = mysqli_query($con, "SELECT * FROM products $categoryFilter where pstatus = 1 LIMIT 10");
                while ($product = mysqli_fetch_assoc($productQuery)) {
                ?>
                    <div class="product">
                        <img src="../admin/<?php echo $product['productimage1'] ?>" alt="<?php echo $product['productname'] ?>">
                        <h4><?php echo $product['productname'] ?></h4>
                        <p>₹<?php echo $product['productprice'] ?></p>
                        <?php if (isset($_SESSION['username'])) { ?>
                            <form action="" method="post">
                                <input type="text" name="productid" value="<?php echo $product['pid'] ?>" hidden>
                                <button class="add-to-cart" name="addCart">Add to Cart</button>
                            </form>
                        <?php } else { ?>
                            <button class="add-to-cart" style="cursor: not-allowed;" disabled>Add to Cart</button>
                        <?php } ?>

                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
    <footer class="footer">
        <h2>THANK YOU FOR VISITING</h2>
        <p>© 2025 My Grocery Store</p>
    </footer>

    <!-- <script src="javascript/script.js"></script> -->
    <script>
        document.querySelector('.dropdown-btn').addEventListener('click', () => {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', (e) => {
            if (!e.target.matches('.dropdown-btn')) {
                document.querySelector('.dropdown-menu').style.display = 'none';
            }
        });

        function loadCategory(categoryId) {
            // Clear the entire page content except for the header/sidebar
            document.getElementById("main").innerHTML = "<h2>Loading products...</h2>";

            fetch(`fetch_products.php?category_id=${categoryId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById("main").innerHTML = html;
                })
                .catch(error => console.error("Error fetching products:", error));
        }

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
                            suggestion.textContent = item.name;
                            suggestion.classList.add("suggestion-item");

                            suggestion.addEventListener("click", function() {
                                searchBar.value = item.name;
                                updateURL(item);
                                performSearch(item.name);
                                suggestionsDiv.innerHTML = "";
                            });

                            suggestionsDiv.appendChild(suggestion);
                        });
                    });
            });

            function updateURL(item) {
                if (item.type === "category") {
                    history.pushState({}, "", `?category=${encodeURIComponent(item.name)}`);
                } else if (item.type === "subcategory") {
                    history.pushState({}, "", `?subcategory=${encodeURIComponent(item.name)}`);
                } else {
                    history.pushState({}, "", `?query=${encodeURIComponent(item.name)}`);
                }
            }

            function performSearch(searchTerm) {
                fetch(`search_results.php?query=${searchTerm}`)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector(".main").innerHTML = html;
                    });
            }

            // Reload main page when search bar is cleared
            searchBar.addEventListener("keyup", function(event) {
                if (searchBar.value.trim() === "") {
                    location.href = "index.php"; // Replace with your main page
                }
            });
        });
    </script>
</body>

</html>