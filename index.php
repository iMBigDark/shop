<?php
include 'config.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin.php");
    } else {
        header("Location: customer.php");
    }
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>