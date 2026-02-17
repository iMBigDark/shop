<?php
include 'config.php';
requireLogin();

$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as items FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Simple Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">Simple Shop</h1>
            <div class="navbar-right">
                <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="customer.php" class="nav-link">Shop</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>My Orders</h2>
        
        <?php if (empty($orders)): ?>
            <p class="no-orders">You haven't placed any orders yet. <a href="customer.php">Start shopping</a></p>
        <?php else: ?>
            <div class="orders-list">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['items'] ?? 0; ?></td>
                                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
