<?php

// Khởi động session để thao tác thông tin đăng nhập
session_start();

// Kết nối cơ sở dữ liệu
include "../DB/connect.php";

// Nhúng lớp SinhVien và SinhVienDAO 
include "../DAO/SinhVienDAO.php";
include "../Model/SinhVien.php";

// Khởi tạo đối tượng DAO dùng chung
$svDAO = new SinhVienDAO($conn);

// Kiểm tra người dùng có đúng quyền NV_Khoa không
if ($_SESSION['role'] != 'NV_Khoa') {
    echo "Bạn không có quyền truy cập trang này!";
    exit;
}

// Lưu mã khoa từ tài khoản đăng nhập
$ma_khoa = $_SESSION['ma_khoa'];

// Lấy danh sách lớp thuộc khoa
$sql_lop = "SELECT * FROM Lop";
$lopResult = mysqli_query($conn, $sql_lop);

// TÌM KIẾM SINH VIÊN
$search = "";
$result = null;

// Nếu người dùng nhấn nút Tìm kiếm
if (isset($_GET['search_btn'])) {

    $search = trim($_GET['search']);

    // Kiểm tra không nhập từ khóa
    if ($search == "") {
        echo "<script>alert('Vui lòng nhập từ khóa tìm kiếm!');</script>";
    } else {
        // Gửi yêu cầu tìm kiếm -> gọi DAO
        $result = $svDAO->search($search, $ma_khoa);

        // Kiểm tra không tìm thấy kết quả
        if (mysqli_num_rows($result) == 0) {
            echo "<script>alert('Không tìm thấy sinh viên nào phù hợp!');</script>";
        }
    }
}

// Nếu chưa tìm kiếm -> mặc định lấy tất cả sinh viên thuộc khoa
if (!$result) {
    $result = $svDAO->getAllByKhoa($ma_khoa);
}

// THÊM SINH VIÊN
if (isset($_POST['them'])) {

    // Lấy dữ liệu từ form thêm
    $ma_sv     = trim($_POST['ma_sv']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $ma_lop    = trim($_POST['ma_lop']);

    // VALIDATE
    // Kiểm tra để trống
    if ($ho_ten == "" || $ngay_sinh == "") {
        echo "<script>alert('Không được để trống họ tên hoặc ngày sinh!');</script>";
        return;
    }

    // Kiểm tra ký tự
    if (!preg_match('/^[A-Za-zÀ-ỹ\s]+$/u', $ho_ten)) {
        echo "<script>alert('Họ tên chỉ được chứa chữ!');</script>";
        return;
    }

    // Kiểm tra định dạng mã SV. Bắt đầu phải là SV
    if (!preg_match('/^SV[0-9]+$/', $ma_sv)) {
        echo "<script>alert('Mã sinh viên phải bắt đầu bằng SV và theo sau là các chữ số!');</script>";
        return;
    }

    // Tính tuổi
    $tuoi = date_diff(date_create($ngay_sinh), date_create(date("Y-m-d")))->y;

    // Tuổi phải nằm trong khoảng 16–60
    if ($tuoi < 16 || $tuoi > 60) {
        echo "<script>alert('Tuổi sinh viên phải từ 16 đến 60!');</script>";
        return;
    }

    // Kiểm tra trùng mã SV
    if ($svDAO->existsByMaSv($ma_sv)) {
        echo "<script>alert('Mã sinh viên đã tồn tại! Vui lòng nhập mã khác!');</script>";
        return; // Dừng xử lý thêm
    }

    /* TẠO ĐỐI TƯỢNG SINHVIEN VÀ GỬI CHO DAO */
    // Tạo đối tượng SinhVien và gán dữ liệu từ form 
    $sv = new SinhVien($ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $ma_lop, $ma_khoa);

    // Gọi lớp DAO để thêm sinh viên vào CSDL
    if ($svDAO->insert($sv)) {
        echo "<script>alert('Thêm sinh viên thành công!'); window.location.href='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể thêm sinh viên!');</script>";
    }
}

// CẬP NHẬT SINH VIÊN
$edit_state = false;  // Biến dùng để kiểm tra có Sửa hay không
$edit_ma_sv = "";
$edit_ho_ten = "";
$edit_ngay_sinh = "";
$edit_gioi_tinh = "";
$edit_ma_lop = "";

// Nếu nhấn nút "Sửa" -> tải dữ liệu sinh viên lên form
if (isset($_GET['edit'])) {
    $edit_state = true;
    $ma_edit = $_GET['edit']; // Lấy mã SV cần sửa

    // Lấy dữ liệu sinh viên qua DAO
    $result_edit = $svDAO->getByMaSv($ma_edit);
    $sv_edit = mysqli_fetch_assoc($result_edit);

    // Gán dữ liệu lên form
    $edit_ma_sv     = $sv_edit['ma_sv'];
    $edit_ho_ten    = $sv_edit['ho_ten'];
    $edit_ngay_sinh = $sv_edit['ngay_sinh'];
    $edit_gioi_tinh = $sv_edit['gioi_tinh'];
    $edit_ma_lop    = $sv_edit['ma_lop'];
}

// Khi nhấn nút "Cập nhật"
if (isset($_POST['capnhat'])) {

    // Lấy dữ liệu sửa từ form
    $ma_sv     = trim($_POST['ma_sv']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $ma_lop    = trim($_POST['ma_lop']);

    // VALIDATE 

    // Kiểm tra để trống
    if ($ho_ten == "" || $ngay_sinh == "") {
        echo "<script>alert('Không được để trống!');</script>";
        return;
    }

    // Kiểm tra ký tự
    if (!preg_match('/^[A-Za-zÀ-ỹ\s]+$/u', $ho_ten)) {
        echo "<script>alert('Họ tên chỉ được chứa chữ!');</script>";
        return;
    }

    // Validate tuổi
    $tuoi = date_diff(date_create($ngay_sinh), date_create(date("Y-m-d")))->y;
    if ($tuoi < 16 || $tuoi > 60) {
        echo "<script>alert('Tuổi sinh viên phải từ 16 đến 60');</script>";
        return;
    }

    // TẠO ĐỐI TƯỢNG SINHVIEN 
    $sv_update = new SinhVien($ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $ma_lop, $ma_khoa);

    // GỌI DAO CẬP NHẬT 
    if ($svDAO->update($sv_update)) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể cập nhật sinh viên!');</script>";
    }
}

// XÓA SINH VIÊN
if (isset($_GET['delete'])) {
    $ma_sv_delete = $_GET['delete']; // Lấy mã SV cần xóa

    // GỌI DAO XỬ LÝ XÓA
    if ($svDAO->delete($ma_sv_delete)) {
        echo "<script>alert('Xóa thành công!'); window.location='nv_khoa.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa sinh viên!');</script>";
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
            <!-- Tên chức năng giao diện -->
            <h3><?php echo $edit_state ? "Sửa sinh viên" : "Thêm sinh viên"; ?></h3>
            <form method="post">
                <!-- Mã sinh viên -->
                <label>Mã SV: </label>
                <input type="text" placeholder="Nhập mã sinh viên. Ví dụ SV01" name="ma_sv" required autofocus
                    value="<?php echo $edit_ma_sv; ?>" <?php echo $edit_state ? 'readonly' : ''; ?>>

                <!-- Họ tên -->
                <label>Họ tên: </label>
                <input type="text" placeholder="Nhập họ tên sinh viên" name="ho_ten" required
                    value="<?php echo $edit_ho_ten; ?>">

                <!-- Ngày sinh -->
                <label>Ngày sinh: </label>
                <input type="date" name="ngay_sinh" required value="<?php echo $edit_ngay_sinh; ?>">

                <!-- Giới tính -->
                <label>Giới tính:</label>
                <select name="gioi_tinh">
                    <option value="Nam" <?php if ($edit_gioi_tinh == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nu" <?php if ($edit_gioi_tinh == 'Nu') echo 'selected'; ?>>Nữ</option>
                </select>

                <!-- Lớp -->
                <label>Lớp:</label>
                <select name="ma_lop">
                    <?php while ($lop = mysqli_fetch_assoc($lopResult)) { ?>
                        <option value="<?php echo $lop['ma_lop']; ?>"
                            <?php if ($lop['ma_lop'] == $edit_ma_lop) echo 'selected'; ?>>
                            <?php echo $lop['ten_lop']; ?>
                        </option>

                    <?php } ?>
                </select>

                <!-- Nút chức năng -->
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
            <input type="text" name="search" placeholder="Tìm kiếm theo mã hoặc họ tên" value="<?php echo $search; ?>">
            <button type="submit" name="search_btn">Tìm kiếm</button>
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
                        <!-- Trả về dữ liệu SV từ CSDL -->
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