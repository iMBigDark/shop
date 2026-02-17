<?php
include 'config.php';
requireLogin();

$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as items FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سفارشات من - فروشگاه ساده</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">فروشگاه ساده</h1>
            <div class="navbar-right">
                <span class="welcome">خوش آمدید، <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="customer.php" class="nav-link">فروشگاه</a>
                <a href="logout.php" class="nav-link">خروج</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>سفارشات من</h2>
        
        <?php if (empty($orders)): ?>
            <p class="no-orders">هنوز سفارشی ثبت نکرده اید. <a href="customer.php">شروع خرید کنید</a></p>
        <?php else: ?>
            <div class="orders-list">
                <table>
                    <thead>
                        <tr>
                            <th>شماره سفارش</th>
                            <th>تعداد اقلام</th>
                            <th>مجموع</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['items'] ?? 0; ?></td>
                                <td><?php echo number_format($order['total_price'], 0); ?> ریال</td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo $order['status'] === 'completed' ? 'تکمیل شد' : ($order['status'] === 'pending' ? 'در حال بررسی' : 'لغو شد'); ?></span></td>
                                <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
