<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Không có quyền!");
}

$ma_nv = $_GET['ma_nv'] ?? '';
if (!$ma_nv) exit("Không xác định nhân viên.");
?>  

<div style="border:1px solid #ccc; padding:15px; width:300px; border-radius:5px;">
    <h3 style="margin-top:0;">Xóa nhân viên</h3>
    <p>Bạn có chắc muốn xóa nhân viên mã NV <?= htmlspecialchars($ma_nv) ?>?</p>
    <form method="POST" action="hosonhanvien.php" style="display:flex; gap:10px;">
        <input type="hidden" name="ma_nv" value="<?= htmlspecialchars($ma_nv) ?>">
        <button type="submit" name="delete">Xóa</button>
        <button type="button" onclick="window.location='hosonhanvien.php'">Hủy</button>
    </form>
</div>
