<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == '' || $password == '') {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute(array($username));
        $user = $stmt->fetch();

        if ($user) {
            $password_match = password_verify($password, $user['password']);
            if ($password_match) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: customer.php");
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود - فروشگاه سجاد</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <div class="auth-container">
        <div class="auth-box">
            <h1>ورود</h1>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="form">
                <input type="text" name="username" placeholder="نام کاربری" required>
                <input type="password" name="password" placeholder="رمز عبور" required>
                <button type="submit" class="btn">ورود</button>
            </form>
            <p>حساب ندارید؟ <a href="register.php">ثبت نام کنید</a></p>
        </div>
    </div>
</body>
</html>
