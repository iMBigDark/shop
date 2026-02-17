<?php
include 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: customer.php");
    exit();
}

$cart_json = $_POST['cart'];
$cart = json_decode($cart_json, true);

if (!$cart || count($cart) == 0) {
    header("Location: customer.php");
    exit();
}

$total = 0;
for ($i = 0; $i < count($cart); $i++) {
    $item = $cart[$i];
    $total = $total + ($item['price'] * $item['quantity']);
}

$user_id = $_SESSION['user_id'];
$sql = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($user_id, $total));

$order_id = $pdo->lastInsertId();

$sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
$stmt_item = $pdo->prepare($sql_item);

for ($i = 0; $i < count($cart); $i++) {
    $item = $cart[$i];
    $stmt_item->execute(array($order_id, $item['id'], $item['quantity'], $item['price']));
    
    $sql_update = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute(array($item['quantity'], $item['id']));
}

$sql_complete = "UPDATE orders SET status = 'completed' WHERE id = ?";
$stmt_complete = $pdo->prepare($sql_complete);
$stmt_complete->execute(array($order_id));

header("Location: order-success.php?order_id=" . $order_id);
exit();
?>
