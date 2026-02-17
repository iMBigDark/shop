<?php
include 'config.php';
requireLogin();

$products = $pdo->query("SELECT * FROM products WHERE stock > 0")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shop - Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">Simple Shop</h1>
            <div class="navbar-right">
                <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button id="cart-btn" class="cart-btn">🛒 Cart (<span id="cart-count">0</span>)</button>
                <a href="my-orders.php" class="nav-link">My Orders</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Available Products</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <div class="image-placeholder"><?php echo strtoupper(substr($product['name'], 0, 1)); ?></div>
                    </div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>', <?php echo $product['price']; ?>)">
                        Add to Cart
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Shopping Cart</h2>
            <div id="cart-items" class="cart-items"></div>
            <div class="cart-summary">
                <h3>Total: $<span id="total">0.00</span></h3>
                <button class="btn btn-success" onclick="checkout()">Checkout</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        const cartBtn = document.getElementById('cart-btn');
        const cartModal = document.getElementById('cart-modal');
        const closeBtn = document.querySelector('.close');

        cartBtn.onclick = () => cartModal.style.display = 'block';
        closeBtn.onclick = () => cartModal.style.display = 'none';
        window.onclick = (e) => {
            if (e.target === cartModal) cartModal.style.display = 'none';
        }
    </script>
</body>
</html>
