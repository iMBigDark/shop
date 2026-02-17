<?php
session_start();

$db_host = 'localhost';
$db_name = 'simple_shop';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

function isAdmin() {
    if (isLoggedIn() && $_SESSION['role'] == 'admin') {
        return true;
    }
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: login.php");
        exit();
    }
}
?>