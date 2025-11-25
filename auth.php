<?php
// Khởi động session để lấy thông tin đăng nhập
session_start();

// Kiểm tra nếu SESSION không có user_id -> Chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Chuyển hướng về lại trang login
    header("Location: login.php");
    exit;
}
