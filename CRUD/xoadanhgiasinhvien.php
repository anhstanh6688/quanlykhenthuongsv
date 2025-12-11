<?php
session_start();
include "../DB/connect.php";
include "../DAO/DanhGiaGVCNDAO.php";
include "../Model/DanhGiaGVCN.php";
$dgDAO = new DanhGiaGVCNDAO($conn);

// Lấy ID đánh giá từ URL
$id = $_GET['id'] ?? '';
if (!$id) die("Không có bản đánh giá nào để xóa!");

// Kiểm tra bản ghi tồn tại
$dg = $dgDAO->getById($id);
if (!$dg) die("Không tìm thấy đánh giá này!");

// Xóa điểm / nhận xét / ngày đánh giá (chỉ reset chứ không xóa cả record)
$success = $dgDAO->resetDanhGia($id);

if ($success) {
    header("Location: ../GVCN/gv_cn.php");
    exit;
} else {
    echo "Lỗi xóa đánh giá!";
}
?>