<?php
include 'config.php';
requireAdmin();

$action = $_GET['action'] ?? '';

if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $description = $_POST['description'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $image = '';

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $target_dir = "uploads/";
            $file_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $file_name;
            }
        }

        if ($name && $price && $stock !== '') {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $description, $stock, $image]);
            header("Location: admin.php");
            exit;
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $description = $_POST['description'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $image = $_POST['old_image'] ?? '';

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $target_dir = "uploads/";
            $file_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $file_name;
            }
        }

        if ($id && $name && $price && $stock !== '') {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $price, $description, $stock, $image, $id]);
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
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت - فروشگاه سجاد</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <nav class="navbar">
        <div class="navbar-container">
            <h1 class="navbar-title">پنل مدیریت</h1>
            <div class="navbar-right">
                <span class="welcome">خوش آمدید، <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="nav-link">خروج</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab('products', this)">محصولات</button>
            <button class="tab-btn" onclick="switchTab('orders', this)">سفارشات</button>
        </div>

        <!-- Products Tab -->
        <div id="products-tab" class="tab-content active">
            <h2><?php echo $editProduct ? 'ویرایش محصول' : 'افزودن محصول جدید'; ?></h2>
            
            <form method="POST" action="admin.php?action=<?php echo $editProduct ? 'edit' : 'add'; ?>" class="form" enctype="multipart/form-data">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                    <input type="hidden" name="old_image" value="<?php echo $editProduct['image']; ?>">
                <?php endif; ?>
                
                <input type="text" name="name" placeholder="نام محصول" value="<?php echo $editProduct['name'] ?? ''; ?>" required>
                <input type="number" name="price" placeholder="قیمت" step="0.01" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                <textarea name="description" placeholder="توضیح" rows="4"><?php echo $editProduct['description'] ?? ''; ?></textarea>
                <input type="number" name="stock" placeholder="موجودی" value="<?php echo $editProduct['stock'] ?? ''; ?>" required>
                <input type="file" name="image" accept="image/*" placeholder="عکس محصول">
                <?php if ($editProduct && $editProduct['image']): ?>
                    <p>عکس موجود: <strong><?php echo htmlspecialchars($editProduct['image']); ?></strong></p>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?php echo $editProduct ? 'به روز رسانی محصول' : 'افزودن محصول'; ?></button>
                <?php if ($editProduct): ?>
                    <a href="admin.php" class="btn">لغو</a>
                <?php endif; ?>
            </form>

            <h2>لیست محصولات</h2>
            <div class="products-list">
                <table>
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام</th>
                            <th>قیمت</th>
                            <th>موجودی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['price'], 0); ?> ریال</td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <a href="admin.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm">ویرایش</a>
                                    <form method="POST" action="admin.php?action=delete" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
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
            <h2>سفارشات اخیر</h2>
            <div class="orders-list">
                <table>
                    <thead>
                        <tr>
                            <th>شماره سفارش</th>
                            <th>مشتری</th>
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
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo $order['items'] ?? 0; ?></td>
                                <td><?php echo number_format($order['total_price'], 0); ?> ریال</td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo $order['status'] === 'completed' ? 'تکمیل شد' : ($order['status'] === 'pending' ? 'در حال بررسی' : 'لغو شد'); ?></span></td>
                                <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
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
