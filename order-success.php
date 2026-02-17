<?php
include 'config.php';
requireLogin();

$orderId = $_GET['order_id'] ?? '';

if ($orderId) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Simple Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">Simple Shop</h1>
            <div class="navbar-right">
                <a href="customer.php" class="nav-link">Continue Shopping</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="success-box">
            <h1>✓ Order Placed Successfully!</h1>
            <?php if ($order): ?>
                <p>Order ID: <strong>#<?php echo $order['id']; ?></strong></p>
                <p>Total Amount: <strong>$<?php echo number_format($order['total_price'], 2); ?></strong></p>
                <p>Status: <strong><?php echo ucfirst($order['status']); ?></strong></p>
                <p>Order Date: <strong><?php echo date('M d, Y', strtotime($order['created_at'])); ?></strong></p>
            <?php endif; ?>
            <p>Thank you for your purchase!</p>
            <div style="margin-top: 20px;">
                <a href="customer.php" class="btn btn-primary">Continue Shopping</a>
                <a href="my-orders.php" class="btn">View My Orders</a>
            </div>
        </div>
    </div>
</body>
</html>
