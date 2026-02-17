<?php
include 'config.php';
requireAdmin();

$action = $_GET['action'] ?? '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $description = $_POST['description'] ?? '';
        $stock = $_POST['stock'] ?? '';

        if ($name && $price && $stock !== '') {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, stock) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $description, $stock]);
            header("Location: admin.php");
            exit;
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $description = $_POST['description'] ?? '';
        $stock = $_POST['stock'] ?? '';

        if ($id && $name && $price && $stock !== '') {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE id = ?");
            $stmt->execute([$name, $price, $description, $stock, $id]);
            header("Location: admin.php");
            exit;
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: admin.php");
            exit;
        }
    }
}

// Get product to edit
$editProduct = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editProduct = $stmt->fetch();
}

$products = $pdo->query("SELECT * FROM products")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.username, COUNT(oi.id) as items FROM orders o JOIN users u ON o.user_id = u.id LEFT JOIN order_items oi ON o.id = oi.order_id GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Simple Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">Admin Dashboard</h1>
            <div class="navbar-right">
                <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab('products')">Products</button>
            <button class="tab-btn" onclick="switchTab('orders')">Orders</button>
        </div>

        <!-- Products Tab -->
        <div id="products-tab" class="tab-content active">
            <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
            
            <form method="POST" action="admin.php?action=<?php echo $editProduct ? 'edit' : 'add'; ?>" class="form">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                <?php endif; ?>
                
                <input type="text" name="name" placeholder="Product Name" value="<?php echo $editProduct['name'] ?? ''; ?>" required>
                <input type="number" name="price" placeholder="Price" step="0.01" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                <textarea name="description" placeholder="Description" rows="4"><?php echo $editProduct['description'] ?? ''; ?></textarea>
                <input type="number" name="stock" placeholder="Stock" value="<?php echo $editProduct['stock'] ?? ''; ?>" required>
                <button type="submit" class="btn btn-primary"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                <?php if ($editProduct): ?>
                    <a href="admin.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h2>Products List</h2>
            <div class="products-list">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <a href="admin.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm">Edit</a>
                                    <form method="POST" action="admin.php?action=delete" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Tab -->
        <div id="orders-tab" class="tab-content">
            <h2>Recent Orders</h2>
            <div class="orders-list">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
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
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo $order['items'] ?? 0; ?></td>
                                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
