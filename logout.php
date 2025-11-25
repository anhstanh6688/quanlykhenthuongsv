<?php
// Khởi động session để thao tác 
session_start();

// Xóa tất cả biến trong session
session_unset();

// Hủy toàn bộ session hiện tại
session_destroy();

// Chuyển hướng người dùng trở về trang đăng nhập
header("Location: login.php");
exit;
