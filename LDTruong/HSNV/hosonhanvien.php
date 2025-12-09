<?php
session_start();

// Kết nối CSDL
include __DIR__ . "/../../DB/connect.php";


// Include Model & DAO
include_once __DIR__ . "/../../Model/NhanVien.php";
include_once __DIR__ . "/../../DAO/NhanVienDAO.php";

// Khởi tạo DAO
$nvDAO = new NhanVienDAO($conn);

// Kiểm tra quyền truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    echo "Bạn không có quyền truy cập trang này!";
    exit;
}

// Mã khoa từ tài khoản đăng nhập
$ma_khoa = $_SESSION['ma_khoa'] ?? '';

// Lấy danh sách khoa nếu cần
$sql_khoa = "SELECT * FROM Khoa";
$khoaResult = mysqli_query($conn, $sql_khoa);

// Helper: escape HTML
if (!function_exists('e')) {
    function e($s)
    {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// ===================== TÌM KIẾM =====================
$search = "";
$result = null;

if (isset($_GET['search_btn'])) {
    $search = trim($_GET['search']);
    if ($search === "") {
        echo "<script>alert('Vui lòng nhập từ khóa tìm kiếm!');</script>";
    } else {
        $result = $nvDAO->search($search, $ma_khoa);
        if (mysqli_num_rows($result) === 0) {
            echo "<script>alert('Không tìm thấy nhân viên nào phù hợp!');</script>";
        }
    }
}

// Nếu chưa tìm kiếm -> lấy tất cả nhân viên
if (!$result) {
    $result = $nvDAO->getAllByKhoa($ma_khoa);
}

// ===================== THÊM NHÂN VIÊN =====================
if (isset($_POST['them'])) {
    $ma_nv     = trim($_POST['ma_nv']);
    $user_id   = trim($_POST['user_id']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $chuc_vu   = trim($_POST['chuc_vu']);

    // VALIDATE
    if ($ho_ten === "" || $ngay_sinh === "") {
        echo "<script>alert('Không được để trống họ tên hoặc ngày sinh!');</script>";
    } elseif (!preg_match('/^[A-Za-zÀ-ỹ\s]+$/u', $ho_ten)) {
        echo "<script>alert('Họ tên chỉ được chứa chữ!');</script>";
    } elseif (!preg_match('/^NV[0-9]+$/', $ma_nv)) {
        echo "<script>alert('Mã nhân viên phải bắt đầu bằng NV và theo sau là số!');</script>";
    } else {
        $tuoi = date_diff(date_create($ngay_sinh), date_create(date("Y-m-d")))->y;
        if ($tuoi < 18 || $tuoi > 65) {
            echo "<script>alert('Tuổi nhân viên phải từ 18 đến 65!');</script>";
        } elseif ($nvDAO->existsByMaNv($ma_nv)) {
            echo "<script>alert('Mã nhân viên đã tồn tại!');</script>";
        } else {
            $nv = new NhanVien([
                'ma_nv' => $ma_nv,
                'user_id' => $user_id,
                'ho_ten' => $ho_ten,
                'ngay_sinh' => $ngay_sinh,
                'gioi_tinh' => $gioi_tinh,
                'chuc_vu' => $chuc_vu,
                'ma_khoa' => $ma_khoa
            ]);

            if ($nvDAO->insert($nv)) {
                echo "<script>alert('Thêm nhân viên thành công!'); window.location.href='hosonhanvien.php';</script>";
            } else {
                echo "<script>alert('Lỗi: Không thể thêm nhân viên!');</script>";
            }
        }
    }
}

// ===================== SỬA NHÂN VIÊN =====================
$edit_state = false;
$edit_ma_nv = $edit_user_id = $edit_ho_ten = $edit_ngay_sinh = $edit_gioi_tinh = $edit_chuc_vu = "";

if (isset($_GET['edit'])) {
    $edit_state = true;
    $ma_edit = $_GET['edit'];
    $res_edit = $nvDAO->getByMaNv($ma_edit);
    $nv_edit = mysqli_fetch_assoc($res_edit);

    $edit_ma_nv     = $nv_edit['ma_nv'];
    $edit_user_id   = $nv_edit['user_id'];
    $edit_ho_ten    = $nv_edit['ho_ten'];
    $edit_ngay_sinh = $nv_edit['ngay_sinh'];
    $edit_gioi_tinh = $nv_edit['gioi_tinh'];
    $edit_chuc_vu   = $nv_edit['chuc_vu'];
}

if (isset($_POST['capnhat'])) {
    $ma_nv     = trim($_POST['ma_nv']);
    $user_id   = trim($_POST['user_id']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $chuc_vu   = trim($_POST['chuc_vu']);

    $tuoi = date_diff(date_create($ngay_sinh), date_create(date("Y-m-d")))->y;
    if ($tuoi < 18 || $tuoi > 65) {
        echo "<script>alert('Tuổi nhân viên phải từ 18 đến 65!');</script>";
    } else {
        $nv_update = new NhanVien([
            'ma_nv' => $ma_nv,
            'user_id' => $user_id,
            'ho_ten' => $ho_ten,
            'ngay_sinh' => $ngay_sinh,
            'gioi_tinh' => $gioi_tinh,
            'chuc_vu' => $chuc_vu,
            'ma_khoa' => $ma_khoa
        ]);

        if ($nvDAO->update($nv_update)) {
            echo "<script>alert('Cập nhật thành công!'); window.location.href='hosonhanvien.php';</script>";
        } else {
            echo "<script>alert('Lỗi: Không thể cập nhật nhân viên!');</script>";
        }
    }
}

// ===================== XÓA NHÂN VIÊN =====================
if (isset($_GET['delete'])) {
    $ma_nv_delete = $_GET['delete'];
    if ($nvDAO->delete($ma_nv_delete)) {
        echo "<script>alert('Xóa thành công!'); window.location.href='hosonhanvien.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa nhân viên!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Hồ sơ nhân viên</title>
</head>

<body>
    <div class="header-bar">
        <h2>Quản lý hồ sơ nhân viên</h2>
        <div class="header-actions">
            <a href="../ld_truong.php" class="home">Trang chủ</a>

            <a href="../../logout.php" class="logout">Đăng xuất</a>
        </div>
    </div>
    <div class="container">
        <!-- FORM THÊM / SỬA -->
        <div class="form-box">
            <h3><?php echo $edit_state ? 'Sửa nhân viên' : 'Thêm nhân viên'; ?></h3>
            <form method="post">
                <label>Mã NV:</label>
                <input placeholder="Ví dụ NV01" type="text" name="ma_nv" required value="<?php echo e($edit_ma_nv); ?>"
                    <?php echo $edit_state ? 'readonly' : ''; ?>>

                <label>User ID:</label>
                <input type="text" name="user_id" value="<?php echo e($edit_user_id); ?>">

                <label>Họ tên:</label>
                <input type="text" name="ho_ten" required value="<?php echo e($edit_ho_ten); ?>">

                <label>Ngày sinh:</label>
                <input type="date" name="ngay_sinh" required value="<?php echo e($edit_ngay_sinh); ?>">

                <label>Giới tính:</label>
                <select name="gioi_tinh">
                    <option value="Nam" <?php if ($edit_gioi_tinh == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nu" <?php if ($edit_gioi_tinh == 'Nu') echo 'selected'; ?>>Nữ</option>
                </select>

                <label>Chức vụ:</label>
                <input placeholder="Ví dụ. Nhân viên/Lãnh đạo khoa" type="text" name="chuc_vu"
                    value="<?php echo e($edit_chuc_vu); ?>">

                <button name="<?php echo $edit_state ? 'capnhat' : 'them'; ?>">
                    <?php echo $edit_state ? 'Cập nhật' : 'Thêm'; ?>
                </button>
            </form>
        </div>

        <br>

        <!-- FORM TÌM KIẾM -->
        <form method="GET">
            <input type="text" name="search" placeholder="Tìm theo mã hoặc tên" value="<?php echo e($search); ?>">
            <button type="submit" name="search_btn">Tìm kiếm</button>
        </form>

        <!-- DANH SÁCH NHÂN VIÊN -->
        <div class="table-box">
            <h3>Danh Sách Nhân Viên</h3>
            <table>
                <tr>
                    <th>Mã NV</th>
                    <th>User ID</th>
                    <th>Họ Tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Chức vụ</th>
                    <th>Thao tác</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo e($row['ma_nv']); ?></td>
                        <td><?php echo e($row['user_id']); ?></td>
                        <td><?php echo e($row['ho_ten']); ?></td>
                        <td><?php echo e($row['ngay_sinh']); ?></td>
                        <td><?php echo e($row['gioi_tinh']); ?></td>
                        <td><?php echo e($row['chuc_vu']); ?></td>
                        <td class="actions">
                            <!-- Nút Sửa -->
                            <a href="hosonhanvien.php?edit=<?= htmlspecialchars($row['ma_nv']) ?>" class="btn-edit">Sửa</a>

                            <!-- Nút Xóa có xác nhận -->
                            <a href="hosonhanvien.php?delete=<?= htmlspecialchars($row['ma_nv']) ?>" class="btn-delete"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa mã nhân viên <?= htmlspecialchars($row['ma_nv']) ?> không?');">
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