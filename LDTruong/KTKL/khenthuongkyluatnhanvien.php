<?php
session_start();
include __DIR__ . "/../../DB/connect.php";

// Nhúng Model & DAO
include_once __DIR__ . "/../../Model/KhenThuongKyLuat.php";
include_once __DIR__ . "/../../DAO/KhenThuongKyLuatDAO.php";

// Kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập!");
}

// Khởi tạo DAO
$dao = new KhenThuongKyLuatDAO($conn);
$rewards = $dao->getAll();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý khen thưởng /kỷ luật nhân viên</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
           <h2 class="title-center">Quản lý khen thưởng / kỷ luật nhân viên</h2>
            <div class="header-actions">
                <a href="../../CRUD/themquyetdinh.php" class="btn-add">Thêm mới</a>
                <a href="../ld_truong.php" class="home">Trang chủ</a>
                <a href="../../logout.php" class="logout"
                    style="background: #ff6b6b; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none;">Đăng
                    xuất</a>
            </div>
        </div>



    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã NV</th>
                    <th>Loại</th>
                    <th>Cấp quản lý</th>
                    <th>Nội dung</th>
                    <th>Ngày</th>
                    <th>Người duyệt</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rewards): ?>
                    <?php foreach ($rewards as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['ma_nv']) ?></td>
                            <td><?= htmlspecialchars($r['loai']) ?></td>
                            <td><?= htmlspecialchars($r['cap_quan_ly']) ?></td>
                            <td><?= htmlspecialchars($r['noi_dung']) ?></td>
                            <td><?= htmlspecialchars($r['ngay']) ?></td>
                            <td><?= htmlspecialchars($r['nguoi_duyet']) ?></td>

                            <td class="actions">
                                <a href="../../CRUD/suaquyetdinh.php?id=<?= $r['id'] ?>" class="btn-edit">Sửa</a>
                                <a href="../../CRUD/xoaquyetdinh.php?id=<?= $r['id'] ?>" class="btn-delete">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Chưa có dữ liệu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>