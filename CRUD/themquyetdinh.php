<?php
session_start();

/* Kết nối CSDL */
include __DIR__ . '/../DB/connect.php';

/* Nhúng Model + DAO để xử lý dữ liệu */
include "../DAO/KhenThuongKyLuatDAO.php";
include "../Model/KhenThuongKyLuat.php";

/* Chỉ Lãnh đạo Trường mới được truy cập */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập!");
}

/* Tạo đối tượng DAO để thao tác với bảng khen thưởng/kỷ luật */
$dao = new KhenThuongKyLuatDAO($conn);

/* Lấy thông tin người duyệt từ tài khoản đăng nhập */
$currentUserId = $_SESSION['user_id'] ?? 0;
$currentUserName = $_SESSION['hoten'] ?? "Không xác định";

$msg = "";

/* Lấy danh sách nhân viên cho autocomplete */
$dsNhanVien = mysqli_query($conn, "SELECT ma_nv, ho_ten FROM nhanvien ORDER BY ma_nv ASC");

/* Xử lý submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'ma_nv'       => trim($_POST['ma_nv']),
        'loai'        => $_POST['loai'],
        'cap_quan_ly' => "Truong",   // SET CỨNG
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

            <!-- ==========================
                 MÃ NHÂN VIÊN - AUTOCOMPLETE
            ========================== -->
            <label for="ma_nv">Mã NV:</label>
            <input list="dsMaNV" placeholder="Ví dụ. NV01" 
                   type="text" id="ma_nv" name="ma_nv"
                   value="<?= htmlspecialchars($_POST['ma_nv'] ?? '') ?>" required>

            <datalist id="dsMaNV">
                <?php while ($nv = mysqli_fetch_assoc($dsNhanVien)): ?>
                    <option value="<?= $nv['ma_nv'] ?>">
                        <?= $nv['ma_nv'] . " - " . $nv['ho_ten'] ?>
                    </option>
                <?php endwhile; ?>
            </datalist>

            <label for="loai">Loại:</label>
            <select id="loai" name="loai" required>
                <option value="Khen thưởng" <?= (($_POST['loai'] ?? '') == 'Khen thưởng') ? 'selected' : '' ?>>
                    Khen thưởng
                </option>
                <option value="Kỷ luật" <?= (($_POST['loai'] ?? '') == 'Kỷ luật') ? 'selected' : '' ?>>
                    Kỷ luật
                </option>
            </select>

            <!-- ==========================
                 CẤP QUẢN LÝ = TRUONG (SET CỨNG)
            ========================== -->
            <label>Cấp quản lý:</label>
            <input type="text" value="Truong" disabled style="background:#eee">

            <input type="hidden" name="cap_quan_ly" value="Truong">

            <label for="noi_dung">Nội dung:</label>
            <textarea id="noi_dung" name="noi_dung"
                required><?= htmlspecialchars($_POST['noi_dung'] ?? '') ?></textarea>

            <label for="ngay">Ngày:</label>
            <input type="date" id="ngay" name="ngay" 
                   value="<?= htmlspecialchars($_POST['ngay'] ?? date('Y-m-d')) ?>">

            <label>Người duyệt:</label>
            <input type="text" value="<?= htmlspecialchars($currentUserId) ?>" disabled>

            <input type="hidden" name="nguoi_duyet" value="<?= $currentUserId ?>">

            <button type="submit">Thêm</button>
        </form>
    </div>
</body>
</html>
