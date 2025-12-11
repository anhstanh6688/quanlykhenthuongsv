<?php
session_start();

// Chỉ hiển thị form, không xử lý SQL
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Không có quyền truy cập!");
}
?>

<div class="modal-form">
    <h3>Thêm nhân viên</h3>
    <form method="POST" action="hosonhanvien.php">
        <label>Mã NV:</label>
        <input type="text" name="ma_nv" required>

        <label>User ID:</label>
        <input type="number" name="user_id">

        <label>Họ tên:</label>
        <input type="text" name="ho_ten" required>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh">

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>

        <label>Chức vụ:</label>
        <input type="text" name="chuc_vu">

        <label>Khoa / Phòng ban:</label>
        <input type="text" name="ma_khoa">

        <button type="submit" name="them">Thêm</button>
    </form>
</div>

