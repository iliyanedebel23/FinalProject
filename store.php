<?php
// Database connection
$mysqli = new mysqli("fdb1027.zettahost.bg", "4587974_gaming", ",U2mb#sw8{Y7TFpC", "4587974_gaming");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch products from the database
$query = "SELECT id, product_name, price, image, category FROM products";
$result = $mysqli->query($query);
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nintendo E-Shop</title>
    <link rel="stylesheet" href="style.css">
    <script src="cart.js"></script>
    <style>
        /* General centering styles */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
            margin: 0;
            background-color: #4778FF;
        }

        header, footer {
            width: 100%;
            background-color: #0b1b2b;
            color: #e0e0e0;
            padding: 20px 0;
        }

        header .logo {
            font-size: 2.5rem;
            color: #d4af37;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
            gap: 20px;
        }

        nav ul li {
            display: inline-block;
        }

        nav ul li a {
            color: #e0e0e0;
            text-decoration: none;
            font-size: 1.1rem;
        }

        nav ul li a:hover {
            color: #d4af37;
        }

        .container {
            width: 1200px; /* Adjust container width */
            margin: 0 auto;
            padding: 20px;
            background-color: #0f2741;
        }

        /* Make product grid responsive */
        .grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background-color: #0b1b2b;
            color: #e0e0e0;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            width: 180px;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .add-to-cart {
            background-color: #d4af37;
            color: #0b1b2b;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-to-cart:hover {
            background-color: #b8860b;
        }

        /* Cart Modal without image */
        .popup-panel {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #0b1b2b;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            color: #ffffff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            width: 300px;
        }

        .checkout-btn {
            background-color: #d4af37;
            color: #0b1b2b;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background-color: #b8860b;
        }

        footer {
            text-align: center;
            position: relative;
            width: 100%;
            bottom: 0;
        }

        footer p {
            margin: 0;
        }

        /* Search Bar Styles */
        .search-bar {
            margin: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #d4af37;
        }

        .search-bar button {
            padding: 10px;
            background-color: #d4af37;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #0b1b2b;
        }

        .search-bar button:hover {
            background-color: #b8860b;
            color: white;
        }

        /* Category Filter */
        .filter-bar {
            margin: 20px;
        }

        .filter-bar button {
            padding: 10px;
            background-color: #d4af37;
            border: none;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
            color: #0b1b2b;
        }

        .filter-bar button:hover {
            background-color: #b8860b;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">Nintendo E-Shop</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="store.php">Products</a></li>
                    <li><a href="#" id="view-cart">View Cart (<span id="cart-count">0</span>)</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a href="account.php">Account (<?php echo $_SESSION['username']; ?>)</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search for products...">
        <button onclick="filterProducts()">Search</button>
    </div>

    <!-- Category Filter Bar -->
    <div class="filter-bar">
        <button onclick="filterByCategory('all')">All</button>
        <button onclick="filterByCategory('Game')">Games</button>
        <button onclick="filterByCategory('Accessory')">Accessories</button>
        <button onclick="filterByCategory('Console')">Consoles</button>
    </div>

    <!-- Main Content -->
    <main>
        <section id="featured-products">
            <h2>Featured Products</h2>
            <div class="grid" id="product-grid">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="product-card" data-category="<?php echo $row['category']; ?>" data-name="<?php echo strtolower($row['product_name']); ?>">
                        <a href="product.php?id=<?php echo $row['id']; ?>">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['product_name']; ?>">
                            <h3><?php echo $row['product_name']; ?></h3>
                        </a>
                        <p>Price: $<?php echo $row['price']; ?></p>
                        <button class="add-to-cart btn" data-product="<?php echo $row['product_name']; ?>" data-price="<?php echo $row['price']; ?>">Add to Cart</button>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>

    <!-- Cart Modal -->
    <div id="cart-modal" class="popup-panel">
        <div class="popup-content">
            <span class="close">&times;</span>
            <h3>Shopping Cart</h3>
            <div id="cart-items"></div>
            <p id="cart-total">Total: $0.00</p>
            <button class="checkout-btn" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Nintendo E-Shop. All rights reserved.</p>
    </footer>

    <script>
        const cart = JSON.parse(localStorage.getItem("cart")) || [];

        document.addEventListener("DOMContentLoaded", function () {
            const cartModal = document.getElementById("cart-modal");
            const cartItemsContainer = document.getElementById("cart-items");
            const cartCount = document.getElementById("cart-count");
            const viewCartButton = document.getElementById("view-cart");
            const closeCartModal = document.querySelector(".close");
            const cartTotal = document.getElementById("cart-total");

            // Function to display cart items
            function displayCart() {
                let total = 0;
                cartItemsContainer.innerHTML = "";
                cart.forEach((item, index) => {
                    total += item.price * item.quantity;
                    const cartItem = `
                        <div class="cart-item">
                            <div class="cart-item-details">
                                <h4>${item.name}</h4>
                                <p>Price: $${item.price}</p>
                                <p>Quantity: ${item.quantity}</p>
                                <button data-index="${index}" class="remove-item">Remove</button>
                            </div>
                        </div>
                    `;
                    cartItemsContainer.innerHTML += cartItem;
                });
                cartTotal.textContent = `Total: $${total.toFixed(2)}`;
            }

            // Function to update the cart
            function updateCart() {
                displayCart();
                cartCount.textContent = cart.length;
                localStorage.setItem("cart", JSON.stringify(cart)); // Store cart in localStorage
            }

            // Event listeners for adding to cart
            document.querySelectorAll(".add-to-cart").forEach(button => {
                button.addEventListener("click", function () {
                    const product = {
                        name: this.parentElement.querySelector("h3").textContent,
                        price: parseFloat(this.parentElement.querySelector("p").textContent.replace("Price: $", "")),
                        quantity: 1
                    };
                    cart.push(product);
                    updateCart();
                });
            });

            // Event listener to remove item from cart
            cartItemsContainer.addEventListener("click", function (event) {
                if (event.target.classList.contains("remove-item")) {
                    const index = event.target.getAttribute("data-index");
                    cart.splice(index, 1);
                    updateCart();
                }
            });

            viewCartButton.addEventListener("click", () => {
                cartModal.style.display = "block";
            });

            closeCartModal.addEventListener("click", () => {
                cartModal.style.display = "none";
            });

            displayCart(); // Load cart from localStorage on page load
        });

        // Search functionality
        function filterProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const productName = product.getAttribute('data-name');
                if (productName.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Category filter functionality
        function filterByCategory(category) {
            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                if (category === 'all' || productCategory === category) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
