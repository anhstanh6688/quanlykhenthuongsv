<?php
// ==========================
// Khởi động session để quản lý đăng nhập và phân quyền
// ==========================
session_start();

// ==========================
// Kết nối CSDL
// ==========================
include __DIR__ . '/../DB/connect.php';

// ==========================
// Nhúng DAO và Model để thao tác dữ liệu
// ==========================
include "../DAO/KhenThuongKyLuatDAO.php";
include "../Model/KhenThuongKyLuat.php";

// ==========================
// Kiểm tra quyền truy cập: chỉ cho phép vai trò "LD_Truong"
// ==========================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập!");
}

// ==========================
// Khởi tạo DAO để thao tác dữ liệu khen thưởng/kỷ luật
// ==========================
$dao = new KhenThuongKyLuatDAO($conn);

// ==========================
// Lấy ID & TÊN người duyệt từ session
// ==========================
$currentUserId = $_SESSION['user_id'] ?? 0;
$currentUserName = $_SESSION['hoten'] ?? "Không xác định";

// ==========================
// Biến lưu thông báo lỗi/thành công
// ==========================
$msg = '';

// ==========================
// Xử lý khi form được submit bằng POST
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'ma_nv'       => trim($_POST['ma_nv']),
        'loai'        => $_POST['loai'],
        'cap_quan_ly' => trim($_POST['cap_quan_ly']) ?: 'Không xác định',
        'noi_dung'    => trim($_POST['noi_dung']),
        'ngay'        => $_POST['ngay'] ?: date('Y-m-d'),
        'nguoi_duyet' => $currentUserId
    ];

    if (!$data['ma_nv'] || !$data['loai'] || !$data['noi_dung']) {
        $msg = "Mã NV, Loại và Nội dung là bắt buộc.";
    } else {
        if ($dao->insert($data)) {
            header("Location: ../LDTruong/KTKL/khenthuongkyluatnhanvien.php");
            exit;
        } else {
            $msg = "Lỗi thêm dữ liệu!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm quyết định</title>
    <link rel="stylesheet" href="../LDTruong/KTKL/style.css">
</head>

<body>
    <div class="header-bar">
        <h2>Thêm khen thưởng/kỷ luật</h2>
        <a href="../LDTruong/KTKL/khenthuongkyluatnhanvien.php" class="btn-cancel">Trở về</a>
    </div>

    <div class="form-box">
        <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form method="post" action="" class="form">

            <label for="ma_nv">Mã NV:</label>
            <input placeholder="Ví dụ. NV01" type="text" id="ma_nv" name="ma_nv"
                value="<?= htmlspecialchars($_POST['ma_nv'] ?? '') ?>" required>

            <label for="loai">Loại:</label>
            <select id="loai" name="loai" required>
                <option value="Khen thưởng" <?= (($_POST['loai'] ?? '') == 'Khen thưởng') ? 'selected' : '' ?>>
                    Khen thưởng
                </option>
                <option value="Kỷ luật" <?= (($_POST['loai'] ?? '') == 'Kỷ luật') ? 'selected' : '' ?>>
                    Kỷ luật
                </option>
            </select>

            <label for="cap_quan_ly">Cấp quản lý:</label>
            <input placeholder="Ví dụ. Truong" type="text" id="cap_quan_ly" name="cap_quan_ly"
                value="<?= htmlspecialchars($_POST['cap_quan_ly'] ?? '') ?>">

            <label for="noi_dung">Nội dung:</label>
            <textarea id="noi_dung" name="noi_dung"
                required><?= htmlspecialchars($_POST['noi_dung'] ?? '') ?></textarea>

            <label for="ngay">Ngày:</label>
            <input type="date" id="ngay" name="ngay" value="<?= htmlspecialchars($_POST['ngay'] ?? date('Y-m-d')) ?>">

            <!-- ==========================
                 NGƯỜI DUYỆT (THÊM MỚI)
                 ========================== -->
            <label>Người duyệt:</label>
            <input type="text" value="<?= htmlspecialchars($currentUserId) ?>" disabled>

            <!-- hidden để gửi ID -->
            <input type="hidden" name="nguoi_duyet" value="<?= $currentUserId ?>">

            <button type="submit">Thêm</button>
        </form>
    </div>
</body>

</html>