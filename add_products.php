<?php
// Database connection
$servername = "fdb1027.zettahost.bg";
$username = "4587974_gaming";
$password = ',U2mb#sw8{Y7TFpC';
$dbname = "4587974_gaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "Access denied. You do not have permission to access this page.";
    exit();
}

// Proceed with the rest of the page (product addition logic)
// Handle form submission for adding a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // File upload handling
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert product into the database
        $sql = "INSERT INTO products (product_name, price, image, category) 
                VALUES ('$product_name', '$price', '$image_name', '$category')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New product added successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    // Delete product query
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    
    if ($stmt->execute()) {
        echo "Product removed successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Fetch products to display them in the catalog
$sql = "SELECT id, product_name, price, image, category FROM products";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store - Add Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Header with Navigation and Search -->
<header>
    <div class="nav-container">
        <div class="logo">
            <a href="store.php"><img src="logo.png" alt="Nintendo Store"></a>
        </div>
        <nav>
            <ul>
                <li><a href="consoles.php">Consoles</a></li>
                <li><a href="games.php">Games</a></li>
                <li><a href="accessories.php">Accessories</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
            <button>Search</button>
        </div>
    </div>
</header>

<!-- Form to Add New Product -->
<section class="add-product">
    <h2>Add a New Product</h2>
    <form action="add_products.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_product">
        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="Console">Console</option>
            <option value="Game">Game</option>
            <option value="Accessory">Accessory</option>
        </select>

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" required>

        <label for="image">Upload Image</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Add Product</button>
    </form>
</section>

<!-- Display the Product Catalog -->
<section class="product-list">
    <h2>Product Catalog</h2>
    <div class="products-grid">
        <?php
        if ($result->num_rows > 0) {
            // Output data of each product
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<img src="uploads/' . $row["image"] . '" alt="' . $row["product_name"] . '">';
                echo '<h3>' . $row["product_name"] . '</h3>';
                echo '<p>Price: $' . $row["price"] . '</p>';
                echo '<p>Category: ' . $row["category"] . '</p>';
                
                // Delete form
                echo '<form action="add_products.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this product?\');">';
                echo '<input type="hidden" name="product_id" value="' . $row["id"] . '">';
                echo '<button type="submit" name="delete_product">Delete Product</button>';
                echo '</form>';
                
                echo '</div>';
            }
        } else {
            echo "No products available.";
        }
        ?>
    </div>
</section>

</body>
</html>
