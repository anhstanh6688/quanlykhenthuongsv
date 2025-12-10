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
   KIỂM TRA QUYỀN + ID
   ============================ */
$id = intval($_GET['id'] ?? 0);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong' || !$id) {
    exit("Không hợp lệ hoặc không có quyền truy cập!");
}

$currentUserId = $_SESSION['user_id'] ?? 0;

/* ============================
   KHỞI TẠO DAO
   ============================ */
$khenThuongDAO = new KhenThuongKyLuatDAO($conn);

/* ============================
   LẤY DỮ LIỆU CŨ
   ============================ */
$record = $khenThuongDAO->getById($id);
if (!$record) {
    exit("Không tìm thấy dữ liệu!");
}

$msg = "";

/* ============================
   XỬ LÝ CẬP NHẬT
   ============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        "ma_nv" => $_POST['ma_nv'] ?? "",
        "loai" => $_POST['loai'] ?? "",
        "cap_quan_ly" => $_POST['cap_quan_ly'] ?: null,
        "noi_dung" => $_POST['noi_dung'] ?? "",
        "ngay" => $_POST['ngay'] ?? null,
        "nguoi_duyet" => $currentUserId
    ];

    if ($khenThuongDAO->update($id, $data)) {
        header("Location: ../LDTruong/KTKL/khenthuongkyluatnhanvien.php");
        exit;
    } else {
        $msg = "Lỗi cập nhật dữ liệu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa quyết định</title>

    <link rel="stylesheet" href="../LDTruong/KTKL/style.css">
</head>

<body>

    <div class="header-bar">
        <h2>Sửa quyết định</h2>
    </div>

    <div class="panel">

        <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form method="post" class="form-edit">

            <div class="form-group">
                <label>Mã NV:</label>
                <input type="text" name="ma_nv" value="<?= htmlspecialchars($record['ma_nv']) ?>" required>
            </div>

            <div class="form-group">
                <label>Loại:</label>
                <select name="loai">
                    <option value="khen thưởng" <?= $record['loai'] == 'khen thưởng' ? 'selected' : '' ?>>Khen thưởng
                    </option>
                    <option value="kỷ luật" <?= $record['loai'] == 'kỷ luật' ? 'selected' : '' ?>>Kỷ luật</option>
                </select>
            </div>

            <div class="form-group">
                <label>Cấp quản lý:</label>
                <input type="text" name="cap_quan_ly" value="<?= htmlspecialchars($record['cap_quan_ly']) ?>">
            </div>

            <div class="form-group">
                <label>Ngày:</label>
                <input type="date" name="ngay" value="<?= htmlspecialchars($record['ngay']) ?>">
            </div>

            <div class="form-group">
                <label>Nội dung:</label>
                <textarea name="noi_dung" required><?= htmlspecialchars($record['noi_dung']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Người duyệt (ID):</label>
                <input type="text" value="<?= $currentUserId ?>" readonly>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-update">Cập nhật</button>
                <a href="../LDTruong/KTKL/khenthuongkyluatnhanvien.php" class="btn-cancel-gray">Hủy</a>
            </div>

        </form>
    </div>

</body>

</html>