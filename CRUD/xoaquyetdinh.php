<?php
session_start();
include __DIR__ . "/../../DB/connect.php";

// Nhúng DAO và Model
include_once __DIR__ . "/../../Model/NhanVien.php";
include_once __DIR__ . "/../../DAO/NhanVienDAO.php";

// Kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập!");
}

$id = intval($_GET['id'] ?? 0);
if (!$id) exit("ID không hợp lệ");

// Khởi tạo DAO
$khenThuongDAO = new KhenThuongKyLuatDAO($conn);

// Lấy thông tin quyết định để hiển thị trong hộp thoại
$data = $khenThuongDAO->getById($id);
if (!$data) exit("Không tìm thấy dữ liệu!");

// Nếu người dùng ấn nút XÓA => thực hiện xoá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if ($khenThuongDAO->delete($id)) {
        header("Location: khenthuongkyluatnhanvien.php?msg=deleted");
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
<link rel="stylesheet" href="./KTKL/style.css?v=<?= time() ?>">
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
            <a href="./KTKL/khenthuongkyluatnhanvien.php" class="btn cancel">Hủy</a>
        </div>
    </form>

</div>

</body>
</html>
