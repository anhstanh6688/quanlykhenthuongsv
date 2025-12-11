<?php
include "../DB/connect.php";
include "../Model/DanhGiaGVCN.php";
include "../DAO/DanhGiaGVCNDAO.php";

$dgDAO = new DanhGiaGVCNDAO($conn);

// Lấy ID đánh giá từ URL
$id = $_GET['id'] ?? '';
if(!$id) {
    echo "<p style='color:red; text-align:center;'>Thiếu ID đánh giá!</p>";
    exit;
}

// Lấy đánh giá qua DAO
$dg = $dgDAO->getById($id);
if(!$dg){
    echo "<p style='color:red; text-align:center;'>Không tìm thấy đánh giá!</p>";
    exit;
}

// Lấy thông tin sinh viên qua DAO
$sv = $dgDAO->getSinhVien($dg->getMaSv());
if(!$sv){
    echo "<p style='color:red; text-align:center;'>Không tìm thấy sinh viên!</p>";
    exit;
}

$error = "";

// Nếu người dùng ấn nút Cập nhật
if(isset($_POST['submit'])){

    $diem = $_POST['diem_ren_luyen'];
    $nhan_xet = $_POST['nhan_xet'];
    
    // Cập nhật giá trị MỚI vào Object đang có (trong bộ nhớ RAM)
    $dg->setDiemRL($diem);
    $dg->setNhanXet($nhan_xet);
    $dg->setNgay(date("Y-m-d"));

    // VALIDATE
    if($diem < 0 || $diem > 100){
        $error = "Điểm rèn luyện phải trong khoảng 0–100!";
    } else {

        // Gọi DAO cập nhật
        // $updateOK = $dgDAO->update($id, $diem, $nhan_xet);
        $updateOK = $dgDAO->update($dg);

        if($updateOK){
            header("Location: ../GVCN/gv_cn.php");
            exit;
        } else {
            $error = "Lỗi cập nhật!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa đánh giá</title>
    <style>
    /* ===== Body căn giữa toàn bộ (căn ngang + dọc) ===== */
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
        color: #333;

        display: flex;
        flex-direction: column;
        align-items: center;
        /* căn giữa theo chiều ngang */
        justify-content: center;
        /* căn giữa theo chiều dọc */
        min-height: 100vh;
        /* chiều cao tối thiểu full màn hình */
    }

    /* ===== Tiêu đề H2 ===== */
    h2 {
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
    }

    /* ===== Form sửa đánh giá căn giữa, có max-width và padding ===== */
    form {
        max-width: 400px;
        width: 100%;
        background-color: white;
        padding: 20px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        /* Đảm bảo form luôn ở giữa */
        margin-left: auto;
        margin-right: auto;
    }

    /* Các label */
    form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #34495e;
    }

    /* Input và textarea */
    form input[type="number"],
    form textarea {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
        resize: vertical;
    }

    /* Nút submit */
    form button[type="submit"] {
        background-color: #3498db;
        border: none;
        color: white;
        padding: 10px 18px;
        font-size: 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: block;
        width: 100%;
    }

    form button[type="submit"]:hover {
        background-color: #2980b9;
    }

    /* Lỗi hiển thị màu đỏ */
    p[style*="color:red"] {
        font-weight: bold;
        margin-bottom: 15px;
    }

    /* Nút quay lại căn giữa */
    .btn.back {
        background-color: #95a5a6;
        padding: 8px 15px;
        margin-top: 15px;
        display: inline-block;
        text-align: center;
        border-radius: 4px;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .btn.back:hover {
        background-color: #7f8c8d;
        text-decoration: none;
    }

    /* Nút quay lại căn giữa, đẹp hơn */
    .btn.back {
        display: block;
        /* chiếm 100% chiều ngang của container */
        width: 180px;
        /* chiều rộng cố định vừa phải */
        margin: 20px auto 0;
        /* căn giữa và cách trên 20px */
        padding: 12px 0;
        /* padding trên dưới */
        background-color: #6c757d;
        /* màu xám nhạt */
        color: #fff;
        /* chữ màu trắng */
        text-align: center;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: all 0.25s ease;
        /* hiệu ứng mượt */
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        /* đổ bóng nhẹ */
        cursor: pointer;
    }

    .btn.back:hover {
        background-color: #5a6268;
        /* màu tối hơn khi hover */
        transform: translateY(-2px);
        /* hơi nhấc lên khi hover */
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        /* đổ bóng sâu hơn */
    }
    </style>
</head>

<body>
    <div class="page-container">

        <div class="header">
            <h2>Sửa đánh giá của <?= htmlspecialchars($sv['ho_ten']) ?> (<?= htmlspecialchars($sv['ma_sv']) ?>)</h2>
            <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
        </div>

        <div class="form-container">
            <form method="POST" class="evaluation-form">

                <div class="form-group">
                    <label>Điểm rèn luyện:</label>
                    <input type="number" name="diem_ren_luyen" min="0" max="100"
                        value="<?= htmlspecialchars($dg->getDiemRL()) ?>" required>
                </div>

                <div class="form-group">
                    <label>Nhận xét:</label>
                    <textarea name="nhan_xet" rows="4"><?= htmlspecialchars($dg->getNhanXet()) ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn submit">Cập nhật</button>
                </div>
            </form>

            <div class="form-group">
                <a href="../GVCN/gv_cn.php" class="btn back">Quay lại</a>
            </div>
        </div>

    </div>
</body>

</html>