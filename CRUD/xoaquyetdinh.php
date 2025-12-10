<?php
session_start();

/* ============================
   KẾT NỐI DATABASE
   ============================ */
include __DIR__ . '/../DB/connect.php';
/* ============================
   NHÚNG MODEL + DAO
   ============================ */
include __DIR__ . '/../Model/KhenThuongKyLuat.php';
include __DIR__ . '/../DAO/KhenThuongKyLuatDAO.php';

/* ============================
   KIỂM TRA QUYỀN
============================ */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập!");
}

$id = intval($_GET['id'] ?? 0);
if (!$id) exit("ID không hợp lệ!");

/* ============================
   KHỞI TẠO DAO
============================ */
$khenThuongDAO = new KhenThuongKyLuatDAO($conn);

/* ============================
   LẤY DỮ LIỆU QUYẾT ĐỊNH
============================ */
$data = $khenThuongDAO->getById($id);
if (!$data) exit("Không tìm thấy dữ liệu!");


/* ============================
   XỬ LÝ XÓA
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {

    if ($khenThuongDAO->delete($id)) {
        header("Location: ../LDTruong/KTKL/khenthuongkyluatnhanvien.php?msg=deleted");
        exit;
    } else {
        exit("Lỗi khi xóa dữ liệu!");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xóa quyết định</title>

    <!-- Đường dẫn đúng đến CSS -->
    <link rel="stylesheet" href="../LDTruong/KTKL/style.css?v=<?= time() ?>">
</head>

<body>

    <div class="delete-box">
        <div class="top-bar">Xóa quyết định</div>

        <p class="confirm-text">Bạn có chắc chắn muốn xóa quyết định này?</p>

        <div class="info">
            <p><strong>Mã NV:</strong> <?= htmlspecialchars($data['ma_nv']) ?></p>
            <p><strong>Loại:</strong> <?= htmlspecialchars($data['loai']) ?></p>
            <p><strong>Ngày:</strong> <?= htmlspecialchars($data['ngay']) ?></p>
        </div>

        <form method="post">
            <div class="buttons">
                <button type="submit" name="confirm_delete" class="btn delete">Xóa</button>
                <a href="../LDTruong/KTKL/khenthuongkyluatnhanvien.php" class="btn cancel">Hủy</a>
            </div>
        </form>
    </div>

</body>

</html>