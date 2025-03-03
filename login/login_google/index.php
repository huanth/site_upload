<?php
// login_google/index.php
session_start();
require_once 'config.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user'])) {
    echo 'Chào mừng ' . $_SESSION['user']['name'] . '<br>';
    echo 'Email: ' . $_SESSION['user']['email'] . '<br>';
    echo 'Hình đại diện: <img src="' . $_SESSION['user']['picture'] . '" alt="Profile Picture"><br>';
    echo '<a href="../logout.php">Đăng xuất</a>';
} else {
    // Hiển thị nút đăng nhập Google nếu người dùng chưa đăng nhập
    echo '<a href="google-login.php">Đăng nhập bằng Google</a>';
}
