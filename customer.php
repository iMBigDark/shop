<?php
include 'config.php';
requireLogin();

$sql = "SELECT * FROM products WHERE stock > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فروشگاه ساده</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">فروشگاه سجاد</h1>
            <div class="navbar-right">
                <span class="welcome">خوش آمدید، <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button id="cart-btn" class="cart-btn">🛒 سبد خرید (<span id="cart-count">0</span>)</button>
                <a href="my-orders.php" class="nav-link">سفارشات من</a>
                <a href="logout.php" class="nav-link">خروج</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>محصولات موجود</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['image'] && file_exists('uploads/' . $product['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="image-placeholder"><?php echo strtoupper(substr($product['name'], 0, 1)); ?></div>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price"><?php echo number_format($product['price'], 0); ?> ریال</p>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="stock">موجودی: <?php echo $product['stock']; ?></p>
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>', <?php echo $product['price']; ?>)">
                        افزودن به سبد
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>سبد خرید</h2>
            <div id="cart-items" class="cart-items"></div>
            <div class="cart-summary">
                <h3>مجموع: <span id="total">0</span> ریال</h3>
                <button class="btn btn-success" onclick="checkout()">تسویه حساب</button>
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
