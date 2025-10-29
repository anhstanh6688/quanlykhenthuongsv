<?php
session_start();
include "../DB/connect.php";

if ($_SESSION['role'] != 'NV_Khoa') {
    echo "Bạn không có quyền truy cập trang này!";
    exit;
}

$ma_khoa = $_SESSION['ma_khoa'];

// Lấy danh sách lớp thuộc khoa
$sql_lop = "SELECT * FROM Lop";
$lopResult = mysqli_query($conn, $sql_lop);

// Tìm kiếm & Lấy danh sách sinh viên thuộc khoa 
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql_sv = "SELECT sv.ma_sv, sv.ho_ten, sv.ngay_sinh, sv.gioi_tinh, l.ten_lop
           FROM sinhvien sv
           JOIN lop l ON sv.ma_lop = l.ma_lop
           WHERE sv.ma_khoa = '$ma_khoa'";

if ($search != '') {
    $sql_sv .= " AND (sv.ma_sv LIKE '%$search%' OR sv.ho_ten LIKE '%$search%')";
}


$result = mysqli_query($conn, $sql_sv);


// THÊM SINH VIÊN
if (isset($_POST['them'])) {
    $ma_sv = $_POST['ma_sv'];
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $ma_lop = $_POST['ma_lop'];

    $sql_insert = "INSERT INTO SinhVien(ma_sv, ho_ten, ngay_sinh, gioi_tinh, ma_lop, ma_khoa)
                   VALUES ('$ma_sv','$ho_ten','$ngay_sinh','$gioi_tinh','$ma_lop','$ma_khoa')";
    if (mysqli_query($conn, $sql_insert)) {
        echo "<script>alert('Thêm sinh viên thành công!'); window.location.href='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể thêm sinh viên!');</script>";
    }
}

// CẬP NHẬT SINH VIÊN
$edit_state = false;
$edit_ma_sv = "";
$edit_ho_ten = "";
$edit_ngay_sinh = "";
$edit_gioi_tinh = "";
$edit_ma_lop = "";

if (isset($_GET['edit'])) {
    $edit_state = true;
    $ma_edit = $_GET['edit'];

    $sql_edit = "SELECT * FROM SinhVien WHERE ma_sv='$ma_edit'";
    $edit_result = mysqli_query($conn, $sql_edit);
    $sv_edit = mysqli_fetch_assoc($edit_result);

    $edit_ma_sv = $sv_edit['ma_sv'];
    $edit_ho_ten = $sv_edit['ho_ten'];
    $edit_ngay_sinh = $sv_edit['ngay_sinh'];
    $edit_gioi_tinh = $sv_edit['gioi_tinh'];
    $edit_ma_lop = $sv_edit['ma_lop'];
}

if (isset($_POST['capnhat'])) {
    $ma_sv = $_POST['ma_sv'];
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $ma_lop = $_POST['ma_lop'];

    $sql_update = "UPDATE sinhvien 
                   SET ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', ma_lop='$ma_lop'
                   WHERE ma_sv='$ma_sv'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể cập nhật!');</script>";
    }
}

// XÓA SINH VIÊN
if (isset($_GET['delete'])) {
    $ma_sv_delete = $_GET['delete'];

    $sql_delete = "DELETE FROM sinhvien WHERE ma_sv='$ma_sv_delete'";

    if (mysqli_query($conn, $sql_delete)) {
        echo "<script>alert('Xóa thành công!'); window.location='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Trang cập nhật hồ sơ sinh viên</title>
</head>

<body>
    <!-- ĐĂNG XUẤT -->
    <div class="header-bar">
        <h2>Quản lý hồ sơ sinh viên</h2>
        <a class="logout" href="../logout.php">Đăng xuất</a>
    </div>


    <div class="container">
        <!-- FORM THÊM -->
        <div class="form-box">
            <h3>Thêm sinh viên</h3>
            <form method="post">
                <label>Mã SV: </label>
                <input type="text" placeholder="Nhập mã sinh viên" name="ma_sv" required
                    value="<?php echo $edit_ma_sv; ?>" <?php echo $edit_state ? 'readonly' : ''; ?>>

                <label>Họ tên: </label>
                <input type="text" placeholder="Nhập họ tên sinh viên" name="ho_ten" required
                    value="<?php echo $edit_ho_ten; ?>">

                <label>Ngày sinh: </label>
                <input type="date" name="ngay_sinh" required value="<?php echo $edit_ngay_sinh; ?>">

                <label>Giới tính:</label>
                <select name="gioi_tinh">
                    <option value="Nam" <?php if ($edit_gioi_tinh == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nu" <?php if ($edit_gioi_tinh == 'Nu') echo 'selected'; ?>>Nữ</option>
                </select>

                <label>Lớp:</label>
                <select name="ma_lop">
                    <?php while ($lop = mysqli_fetch_assoc($lopResult)) { ?>
                        <option value="<?php echo $lop['ma_lop']; ?>"
                            <?php if ($lop['ma_lop'] == $edit_ma_lop) echo 'selected'; ?>>
                            <?php echo $lop['ten_lop']; ?>
                        </option>

                    <?php } ?>
                </select>

                <?php if ($edit_state == false) { ?>
                    <button name="them">Thêm</button>
                <?php } else { ?>
                    <button name="capnhat">Cập nhật</button>
                <?php } ?>
            </form>
        </div>
        <br>

        <!-- FORM TÌM KIẾM  -->
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Tìm kiếm theo mã SV hoặc họ tên"
                value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <!-- FORM LỌC THEO LỚP -->


        <!-- HIỂN THỊ DANH SÁCH SV -->
        <div class="table-box">
            <h3>Danh Sách Sinh Viên</h3>

            <table>
                <tr>
                    <th>Mã SV</th>
                    <th>Họ Tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Lớp</th>
                    <th>Thao tác</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['ma_sv']; ?></td>
                        <td><?php echo $row['ho_ten']; ?></td>
                        <td><?php echo $row['ngay_sinh']; ?></td>
                        <td><?php echo $row['gioi_tinh']; ?></td>
                        <td><?php echo $row['ten_lop']; ?></td>
                        <td class="actions">
                            <!-- SỬA SINH VIÊN -->
                            <a href="nv_khoa.php?edit=<?php echo $row['ma_sv']; ?>">Sửa</a>
                            <!-- XÓA SINH VIÊN -->
                            <a href="nv_khoa.php?delete=<?php echo $row['ma_sv']; ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này không?');">
                                Xóa
                            </a>
                        </td>

                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>

</html>