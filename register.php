<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($username == '' || $email == '' || $password == '' || $confirm_password == '') {
        $error = 'Please fill in all fields';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else if ($password != $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array($username, $email, $hashed_password));
        
        if ($result) {
            $success = 'Registration successful! <a href=\"login.php\">Login here</a>';
        } else {
            $error = 'Registration failed. Username or email may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نام - فروشگاه ساده</title>
    <link rel="stylesheet" href="style.css">
</head>
<body dir="rtl">
    <div class="auth-container">
        <div class="auth-box">
            <h1>ثبت نام</h1>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" class="form">
                <input type="text" name="username" placeholder="نام کاربری" required>
                <input type="email" name="email" placeholder="ایمیل" required>
                <input type="password" name="password" placeholder="رمز عبور" required>
                <input type="password" name="confirm_password" placeholder="تایید رمز عبور" required>
                <button type="submit" class="btn">ثبت نام</button>
            </form>
            <p>حساب دارید؟ <a href="login.php">وارد شوید</a></p>
        </div>
    </div>
</body>
</html>
