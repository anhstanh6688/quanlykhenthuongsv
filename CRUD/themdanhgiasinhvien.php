<?php
session_start();
$gv_chu_nhiem = $_SESSION['user_id']; 

include "../DB/connect.php";

// NHÚNG MODEL + DAO
include "../Model/DanhGiaGVCN.php";
include "../DAO/DanhGiaGVCNDAO.php";

$dgDAO = new DanhGiaGVCNDAO($conn);

// Lấy mã SV từ URL
$ma_sv = $_GET['ma_sv'] ?? '';
if (!$ma_sv) {
    echo "<p style='color:red; text-align:center;'>Thiếu mã sinh viên!</p>";
    exit;
}

// Lấy thông tin sinh viên
$sv = $dgDAO->getSinhVien($ma_sv);
if (!$sv) {
    echo "<p style='color:red; text-align:center;'>Không tìm thấy sinh viên!</p>";
    exit;
}

// Lấy bản đánh giá gần nhất → TRẢ VỀ OBJECT hoặc null
$dg = $dgDAO->getLatestByMaSv($ma_sv);

$error = "";
$success = "";

// Kiểm tra cho phép thêm
$can_add = (
    !$dg ||
    ($dg->getDiemRL() === null || $dg->getNhanXet() === null)
);


// Nếu submit form
if ($can_add && isset($_POST['submit'])) {
    $result = false;
    $diem = $_POST['diem_ren_luyen'];
    $nhan_xet = $_POST['nhan_xet'];

    // INSERT
    if (!$dg) {

        $nam_hoc = $_POST['nam_hoc'];
        $hoc_ky  = $_POST['hoc_ky'];

        $new = new DanhGiaGVCN(
        null,           // id
        $ma_sv,         // ma_sv
        $diem,          // diem
        $nhan_xet,      // nhận xét
        $nam_hoc,       // năm học
        $hoc_ky,        // học kỳ
        date("Y-m-d"),  // ngày
        $gv_chu_nhiem   // GVCN — BẮT BUỘC
);


        $result = $dgDAO->insert($new);
    }

    // UPDATE
    else if ($dg->getDiemRL() === null && $dg->getNhanXet() === null) {

        $dg->setDiemRL($diem);
        $dg->setNhanXet($nhan_xet);
        $dg->setNgay(date("Y-m-d"));

        $result = $dgDAO->update($dg);
    }

    // Nếu ok → quay về trang chính
    if ($result) {
    header("Location: ../GVCN/gv_cn.php");
    exit;
} else {
    echo "Lỗi thêm/cập nhật đánh giá! " . $conn->error;
}

}

?>




<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm đánh giá</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        background: #eef2f7;
        padding: 30px;
    }

    /* ----------------------------
   TIÊU ĐỀ
----------------------------- */
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
        font-size: 24px;
    }

    /* ----------------------------
   FORM CHÍNH
----------------------------- */
    form {
        max-width: 500px;
        margin: 0 auto;
        background: #fff;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    form label {
        display: block;
        margin-top: 12px;
        font-weight: bold;
        color: #444;
    }

    form input[type="number"],
    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    form textarea {
        resize: vertical;
    }

    /* ----------------------------
   NÚT SUBMIT
----------------------------- */
    form button {
        width: 100%;
        margin-top: 20px;
        padding: 12px;
        background: #007bff;
        border: none;
        color: white;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.25s;
    }

    form button:hover {
        background: #1e7e34;
    }

    /* ----------------------------
   NÚT QUAY LẠI
----------------------------- */
    .btn.back {
        display: block;
        width: 150px;
        margin: 25px auto 0;
        text-align: center;
        background: #6c757d;
        padding: 10px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        transition: 0.25s;
    }

    .btn.back:hover {
        background: #545b62;
    }
    </style>
</head>

<body>
    <div class="page-container">

        <div class="header">
            <h2><?php echo "Thêm đánh giá cho " . htmlspecialchars($sv['ho_ten']) . " (" . htmlspecialchars($ma_sv) . ")"; ?>
            </h2>
            <?php 
        if ($error) echo "<p class='error-msg'>$error</p>"; 
        if ($success) echo "<p class='success-msg'>$success</p>";
        ?>
        </div>

        <?php if ($can_add): ?>
        <div class="form-container">
            <form method="POST" class="evaluation-form">

                <div class="form-group">
                    <label>Điểm rèn luyện:</label>
                    <input type="number" name="diem_ren_luyen" min="0" max="100" required
                        value="<?php echo $dg ? $dg->getDiemRL() : ''; ?>">

                </div>

                <div class="form-group">
                    <label>Nhận xét:</label>
                    <textarea name="nhan_xet" rows="4"><?php echo $dg ? $dg->getNhanXet() : ''; ?></textarea>

                </div>


                <?php if (!$dg): // Nếu chưa có bản ghi → nhập năm học/học kỳ ?>
                <div class="form-group">
                    <label>Năm học:</label>
                    <input type="text" name="nam_hoc" required>
                </div>

                <div class="form-group">
                    <label>Học kỳ:</label>
                    <input type="text" name="hoc_ky" required>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn submit">Thêm/Cập nhật đánh giá</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <a href="../GVCN/gv_cn.php" class="btn back">Quay lại</a>
        </div>

    </div>
</body>

</html>