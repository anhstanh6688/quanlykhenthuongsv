<?php
session_start();
include __DIR__ . "/../DB/connect.php";

// BẮT BUỘC PHẢI ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// PHẢI ĐÚNG ROLE MỚI VÀO
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    echo "Bạn không có quyền truy cập trang này!";
    exit;
}

// Escape
function e($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

$username = e($_SESSION['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Lãnh đạo trường</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>

<body>
    <div class="header-bar">
        <h2>Trang Lãnh đạo trường</h2>
        <div>
            <span style="margin-right:12px;">Xin chào, ldtruong</span>
            <a class="logout" href="../logout.php">Đăng xuất</a>
        </div>
    </div>

    <div class="container">
        <div class="form-box" style="text-align:center;">
            <h3>Quản lý</h3>
            <p>Chọn chức năng bên dưới:</p>
            <div style="display:flex; gap:12px; justify-content:center; margin-top:16px; flex-wrap:wrap;">
                <a href="./HSNV/hosonhanvien.php"><button type="button">Quản lý hồ sơ nhân viên</button></a>
                <a href="./KTKL/khenthuongkyluatnhanvien.php"><button type="button">Quản lý khen thưởng / kỷ
                        luật</button></a>
            </div>
        </div>
    </div>
</body>

</html>