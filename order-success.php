<?php
include 'config.php';
requireLogin();

$order_id = $_GET['order_id'];
$order = null;

if ($order_id) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, user_id, total_price, status, created_at FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($order_id, $user_id));
    $order = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سفارش ثبت شد - فروشگاه ساده</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">فروشگاه ساده</h1>
            <div class="navbar-right">
                <a href="customer.php" class="nav-link">ادامه خرید</a>
                <a href="logout.php" class="nav-link">خروج</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="success-box">
            <h1>✓ سفارش با موفقیت ثبت شد!</h1>
            <?php if ($order): ?>
                <p>شماره سفارش: <strong>#<?php echo $order['id']; ?></strong></p>
                <p>مبلغ کل: <strong><?php echo number_format($order['total_price'], 0); ?> ریال</strong></p>
                <p>وضعیت: <strong><?php 
                    if ($order['status'] == 'completed') {
                        echo 'تکمیل شد';
                    } else {
                        echo 'در انتظار';
                    }
                ?></strong></p>
                <p>تاریخ سفارش: <strong><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></strong></p>
            <?php endif; ?>
            <p>از خرید شما سپاسگزاریم!</p>
            <div style="margin-top: 20px;">
                <a href="customer.php" class="btn btn-primary">ادامه خرید</a>
                <a href="my-orders.php" class="btn">نمایش سفارشات من</a>
            </div>
        </div>
    </div>
</body>
</html>
