<?php
include 'config.php';
requireAdmin();

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if ($action == 'add') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];
        $image = '';

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = uploadImage($_FILES['image']);
        }

        if ($name != '' && $price != '' && $stock != '') {
            $sql = "INSERT INTO products (name, price, description, stock, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($name, $price, $description, $stock, $image));
            header("Location: admin.php");
            exit();
        }
    }
    
    if ($action == 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];
        $image = $_POST['old_image'];

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = uploadImage($_FILES['image']);
        }

        if ($id != '' && $name != '' && $price != '' && $stock != '') {
            $sql = "UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($name, $price, $description, $stock, $image, $id));
            header("Location: admin.php");
            exit();
        }
    }
    
    if ($action == 'delete') {
        $id = $_POST['id'];
        if ($id != '') {
            $sql_delete_items = "DELETE FROM order_items WHERE product_id = ?";
            $stmt_items = $pdo->prepare($sql_delete_items);
            $stmt_items->execute(array($id));
            
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($id));
            header("Location: admin.php");
            exit();
        }
    }
}

function uploadImage($file) {
    $target_dir = "uploads/";
    $file_name = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $file_name;
    }
    return '';
}

$edit_product = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    $edit_product = $stmt->fetch();
}

$sql = "SELECT * FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();

$sql = "SELECT o.id, o.user_id, o.total_price, o.status, o.created_at, u.username, COUNT(oi.id) as item_count FROM orders o JOIN users u ON o.user_id = u.id LEFT JOIN order_items oi ON o.id = oi.order_id GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll();
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
            <h2><?php if ($edit_product) { echo 'ویرایش محصول'; } else { echo 'افزودن محصول جدید'; } ?></h2>
            
            <form method="POST" action="<?php if ($edit_product) { echo 'admin.php?action=edit'; } else { echo 'admin.php?action=add'; } ?>" class="form" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <input type="hidden" name="old_image" value="<?php echo $edit_product['image']; ?>">
                <?php endif; ?>
                
                <input type="text" name="name" placeholder="نام محصول" value="<?php if ($edit_product) { echo $edit_product['name']; } ?>" required>
                <input type="number" name="price" placeholder="قیمت" step="0.01" value="<?php if ($edit_product) { echo $edit_product['price']; } ?>" required>
                <textarea name="description" placeholder="توضیح" rows="4"><?php if ($edit_product) { echo $edit_product['description']; } ?></textarea>
                <input type="number" name="stock" placeholder="موجودی" value="<?php if ($edit_product) { echo $edit_product['stock']; } ?>" required>
                <input type="file" name="image" accept="image/*" placeholder="عکس محصول">
                <?php if ($edit_product && $edit_product['image']): ?>
                    <p>عکس موجود: <strong><?php echo htmlspecialchars($edit_product['image']); ?></strong></p>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?php if ($edit_product) { echo 'به روز رسانی محصول'; } else { echo 'افزودن محصول'; } ?></button>
                <?php if ($edit_product): ?>
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
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
