<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Không có quyền!");
}

// Nhận dữ liệu edit từ GET
$edit = $_GET['edit'] ?? '';
if (!$edit) exit("Không xác định nhân viên!");

?>

<div class="modal-form">
    <h3>Sửa nhân viên</h3>
    <form method="POST" action="hosonhanvien.php">
        <input type="hidden" name="ma_nv" value="<?= htmlspecialchars($edit) ?>">

        <label>User ID:</label>
        <input type="number" name="user_id" value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>">

        <label>Họ tên:</label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($_GET['ho_ten'] ?? '') ?>" required>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($_GET['ngay_sinh'] ?? '') ?>">

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam" <?= (($_GET['gioi_tinh'] ?? '')=='Nam')?'selected':'' ?>>Nam</option>
            <option value="Nữ" <?= (($_GET['gioi_tinh'] ?? '')=='Nữ')?'selected':'' ?>>Nữ</option>
        </select>

        <label>Chức vụ:</label>
        <input type="text" name="chuc_vu" value="<?= htmlspecialchars($_GET['chuc_vu'] ?? '') ?>">

        <label>Khoa / Phòng ban:</label>
        <input type="text" name="ma_khoa" value="<?= htmlspecialchars($_GET['ma_khoa'] ?? '') ?>">

        <button type="submit" name="capnhat">Cập nhật</button>
    </form>
</div>
