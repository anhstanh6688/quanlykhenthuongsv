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
// Lấy ID người dùng hiện tại từ session (người duyệt)
// ==========================
$currentUserId = $_SESSION['user_id'] ?? 0;

// ==========================
// Biến lưu thông báo lỗi/thành công
// ==========================
$msg = '';

// ==========================
// Xử lý khi form được submit bằng phương thức POST
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thu thập dữ liệu từ form
    $data = [
        'ma_nv'       => trim($_POST['ma_nv']),                        // Mã nhân viên
        'loai'        => $_POST['loai'],                               // Loại: khen thưởng / kỷ luật
        'cap_quan_ly' => trim($_POST['cap_quan_ly']) ?: 'Không xác định', // Cấp quản lý (nếu trống thì gán mặc định)
        'noi_dung'    => trim($_POST['noi_dung']),                     // Nội dung chi tiết
        'ngay'        => $_POST['ngay'] ?: date('Y-m-d'),              // Ngày áp dụng (mặc định hôm nay)
        'nguoi_duyet' => $currentUserId                                // Người duyệt (lấy từ session)
    ];

    // Kiểm tra dữ liệu bắt buộc
    if (!$data['ma_nv'] || !$data['loai'] || !$data['noi_dung']) {
        $msg = "Mã NV, Loại và Nội dung là bắt buộc.";
    } else {
        // Thêm dữ liệu vào CSDL
        if ($dao->insert($data)) {
            // Nếu thành công thì chuyển hướng về trang danh sách
            header("Location: ../cnpm/LDTruong/KTKL/khenthuongkyluatnhanvien.php");
            exit;
            // include "../cnpm/LDTruong/KTKL/khenthuongkyluatnhanvien.php";
        } else {
            // Nếu thất bại thì báo lỗi
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
    <!-- Liên kết đến file CSS để định dạng giao diện -->
    <link rel="stylesheet" href="../LDTruong/KTKL/style.css">
</head>

<body>
    <!-- ==========================
         Thanh tiêu đề (Header)
         ========================== -->
    <div class="header-bar">
        <h2>Thêm khen thưởng/kỷ luật</h2>
        <!-- Nút trở về trang danh sách -->
        <a href="../LDTruong/KTKL/khenthuongkyluatnhanvien.php" class="btn-cancel">Trở về</a>

    </div>

    <!-- ==========================
         Nội dung chính (Form thêm mới)
         ========================== -->
    <div class="form-box">
        <!-- Hiển thị thông báo lỗi/thành công nếu có -->
        <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <!-- Form nhập dữ liệu -->
        <form method="post" action="" class="form">
            <!-- Mã nhân viên -->
            <label for="ma_nv">Mã NV:</label>
            <input type="text" id="ma_nv" name="ma_nv" value="<?= htmlspecialchars($_POST['ma_nv'] ?? '') ?>" required>

            <!-- Loại quyết định -->
            <label for="loai">Loại:</label>
            <select id="loai" name="loai" required>
                <option value="Khen thưởng" <?= (($_POST['loai'] ?? '') == 'Khen thưởng') ? 'selected' : '' ?>>Khen
                    thưởng</option>
                <option value="Kỷ luật" <?= (($_POST['loai'] ?? '') == 'Kỷ luật') ? 'selected' : '' ?>>Kỷ luật</option>
            </select>

            <!-- Cấp quản lý -->
            <label for="cap_quan_ly">Cấp quản lý:</label>
            <input placeholder="Ví dụ. Khoa/Truong" type="text" id="cap_quan_ly" name="cap_quan_ly"
                value="<?= htmlspecialchars($_POST['cap_quan_ly'] ?? '') ?>">

            <!-- Nội dung -->
            <label for="noi_dung">Nội dung:</label>
            <textarea id="noi_dung" name="noi_dung"
                required><?= htmlspecialchars($_POST['noi_dung'] ?? '') ?></textarea>

            <!-- Ngày áp dụng -->
            <label for="ngay">Ngày:</label>
            <input type="date" id="ngay" name="ngay" value="<?= htmlspecialchars($_POST['ngay'] ?? date('Y-m-d')) ?>">

            <!-- Nút submit -->
            <button type="submit">Thêm</button>
        </form>
    </div>
</body>

</html>