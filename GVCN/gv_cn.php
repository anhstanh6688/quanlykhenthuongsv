<?php
session_start();
include "../DB/connect.php";
include "../DAO/DanhGiaGVCNDAO.php";


// Kiểm tra phân quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'GV_CN') {
    echo "Bạn không có quyền truy cập!";
    exit;
}

$dao = new DanhGiaGVCNDAO($conn);

// =========================
// NHẬN THAM SỐ LỌC
// =========================
$lop      = $_GET['lop']      ?? "";
$nam_hoc  = $_GET['nam_hoc']  ?? "";
$hoc_ky   = $_GET['hoc_ky']   ?? "";
$search   = $_GET['search']   ?? "";

// =========================
// LẤY MENU DROPDOWN
// =========================
$dsLop      = $dao->getAllLop();
$dsNamHoc   = $dao->getAllNamHoc();
$dsHocKy    = $dao->getAllHocKy();

// =========================
// LẤY DANH SÁCH SINH VIÊN + ĐÁNH GIÁ
// =========================
$dsSinhVien = $dao->getDanhSachSinhVien($lop, $nam_hoc, $hoc_ky, $search);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách sinh viên GVCN</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">

        <!-- MENU -->
        <div class="sidebar">
            <h2>MENU</h2>
            <ul>
                <li>Tổng quan</li>
                <li>Thời khóa biểu</li>
                <li>Đánh giá</li>
                <li>Học tập</li>
                <li>Điểm danh</li>
                <li>Thông báo</li>
            </ul>
        </div>

        <div class="main-content">

            <div class="header">
                <h2>GIÁO VIÊN CHỦ NHIỆM</h2>
            </div>

            <!-- FORM LỌC -->
            <form method="GET" class="filter-form">

                <!-- Lớp -->
                <div class="form-group">
                    <label>Lớp</label>
                    <select name="lop">
                        <option value="">-- Chọn lớp --</option>
                        <?php foreach ($dsLop as $row): ?>
                        <option value="<?= $row['ma_lop'] ?>" <?= $row['ma_lop']==$lop ? 'selected':'' ?>>
                            <?= $row['ten_lop'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Năm học -->
                <div class="form-group">
                    <label>Năm học</label>
                    <select name="nam_hoc">
                        <option value="">-- Chọn năm học --</option>
                        <?php foreach ($dsNamHoc as $row): ?>
                        <option value="<?= $row['nam_hoc'] ?>" <?= $row['nam_hoc']==$nam_hoc ? 'selected':'' ?>>
                            <?= $row['nam_hoc'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Học kỳ -->
                <div class="form-group">
                    <label>Học kỳ</label>
                    <select name="hoc_ky">
                        <option value="">-- Chọn học kỳ --</option>
                        <?php foreach ($dsHocKy as $row): ?>
                        <option value="<?= $row['hoc_ky'] ?>" <?= $row['hoc_ky']==$hoc_ky ? 'selected':'' ?>>
                            <?= $row['hoc_ky'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tìm kiếm -->
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="btn-row">
                    <button type="submit">Tìm kiếm</button>
                    <a href="../logout.php" class="logout-btn">Đăng xuất</a>
                </div>

            </form>

            <h2 class="headera">DANH SÁCH SINH VIÊN</h2>

            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã SV</th>
                            <th>Họ và tên</th>
                            <th>Lớp</th>
                            <th>Năm học</th>
                            <th>Học kỳ</th>
                            <th>Điểm RL</th>
                            <th>Nhận xét</th>
                            <th>Ngày</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                if (count($dsSinhVien) == 0) {
                    echo "<tr><td colspan='10' class='no-data'>Không có dữ liệu</td></tr>";
                } else {
                    $stt = 1;
                    foreach ($dsSinhVien as $sv):
                ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td><?= $sv['ma_sv'] ?></td>
                            <td><?= $sv['ho_ten'] ?></td>
                            <td><?= $sv['ten_lop'] ?></td>
                            <td><?= $sv['nam_hoc'] ?></td>
                            <td><?= $sv['hoc_ky'] ?></td>
                            <td><?= $sv['diem_ren_luyen'] ?></td>
                            <td><?= $sv['nhan_xet'] ?></td>
                            <td><?= $sv['ngay'] ?></td>
                            <td>
                                <a class="btn add" href="../CRUD/themdanhgiasinhvien.php?ma_sv=<?= $sv['ma_sv'] ?>">
                                    Thêm
                                </a>

                                <?php if ($sv['id'] != null): ?>
                                <a class="btn edit" href="../CRUD/suadanhgiasinhvien.php?id=<?= $sv['id'] ?>">
                                    Sửa
                                </a>

                                <a class="btn delete" href="../CRUD/xoadanhgiasinhvien.php?id=<?= $sv['id'] ?>"
                                    onclick="return confirm('Xóa đánh giá này?')">
                                    Xóa
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                    endforeach;
                }
                ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

</html>