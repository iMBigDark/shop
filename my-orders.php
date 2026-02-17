<?php
include 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$sql = "SELECT o.id, o.user_id, o.total_price, o.status, o.created_at, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($user_id));
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
                                <td><?php echo $order['item_count']; ?></td>
                                <td><?php echo number_format($order['total_price'], 0); ?> ریال</td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php 
                                    if ($order['status'] == 'completed') {
                                        echo 'تکمیل شد';
                                    } else if ($order['status'] == 'pending') {
                                        echo 'در حال بررسی';
                                    } else {
                                        echo 'لغو شد';
                                    }
                                ?></span></td>
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
