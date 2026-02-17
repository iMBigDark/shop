<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Simple Shop</h1>
        
        <div class="products">
            <?php
            include 'config.php';
            
            $stmt = $pdo->query("SELECT * FROM products");
            while ($product = $stmt->fetch()) {
                echo '<div class="product">';
                echo '<h2>' . htmlspecialchars($product['name']) . '</h2>';
                echo '<p class="price">$' . number_format($product['price'], 2) . '</p>';
                echo '<p>' . htmlspecialchars($product['description']) . '</p>';
                echo '<button onclick="addToCart(' . $product['id'] . ', \'' . addslashes(htmlspecialchars($product['name'])) . '\', ' . $product['price'] . ')">Add to Cart</button>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="cart">
            <h2>Shopping Cart</h2>
            <ul id="cart-items"></ul>
            <div>Total: $<span id="cart-total">0.00</span></div>
            <button id="checkout-btn">Checkout</button>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>