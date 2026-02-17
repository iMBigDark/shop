<?php
include 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = json_decode($_POST['cart'] ?? '[]', true);
    
    if (empty($cart)) {
        header("Location: customer.php");
        exit;
    }

    // Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $orderId = $pdo->lastInsertId();

    // Add order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        
        // Update product stock
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStmt->execute([$item['quantity'], $item['id']]);
    }

    // Mark order as completed (in a real system, this would be after payment)
    $completeStmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $completeStmt->execute([$orderId]);

    header("Location: order-success.php?order_id=" . $orderId);
    exit;
}
?>
