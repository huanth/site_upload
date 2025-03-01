<?php
// Bắt đầu session
session_start();

// Xóa tất cả biến session
$_SESSION = [];

// Hủy session
session_destroy();

// Xóa cookie session nếu có
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Chuyển hướng về trang login
header('Location: /login');
exit;
