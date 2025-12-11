<?php
session_start();

// ====================== KẾT NỐI CSDL ======================
include __DIR__ . "/../../DB/connect.php";

// Include Model & DAO
include_once __DIR__ . "/../../Model/NhanVien.php";
include_once __DIR__ . "/../../DAO/NhanVienDAO.php";

// Khởi tạo DAO
$nvDAO = new NhanVienDAO($conn);

// ====================== KIỂM TRA QUYỀN ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'LD_Truong') {
    exit("Bạn không có quyền truy cập trang này!");
}

$ma_khoa_session = $_SESSION['ma_khoa'] ?? '';
$user_session_id = $_SESSION['user_id'] ?? 0;

// Escape HTML
function e($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// ====================== TÌM KIẾM ======================
$search = "";
if (isset($_GET['search_btn'])) {
    $search = trim($_GET['search']);
    if ($search === "") {
        echo "<script>alert('Vui lòng nhập từ khóa tìm kiếm!');</script>";
    } else {
        $result = $nvDAO->search($search, $ma_khoa_session);
        if (mysqli_num_rows($result) === 0) {
            echo "<script>alert('Không tìm thấy nhân viên nào phù hợp!');</script>";
        }
    }
}
if (!isset($result)) $result = $nvDAO->getAllByKhoa($ma_khoa_session);

// ====================== THÊM NHÂN VIÊN ======================
if (isset($_POST['them'])) {

    $ma_nv     = trim($_POST['ma_nv']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $chuc_vu   = trim($_POST['chuc_vu']);

    // mã khoa lấy từ hidden input + fallback session
    $ma_khoa = $_POST['ma_khoa'] ?? $ma_khoa_session;

    // User ID tự lấy từ session
    $user_id = $user_session_id;

    // VALIDATION
    if ($ho_ten === "" || $ngay_sinh === "") {
        echo "<script>alert('Không được để trống họ tên hoặc ngày sinh!');</script>";
    } elseif (!preg_match('/^[A-Za-zÀ-ỹ\s]+$/u', $ho_ten)) {
        echo "<script>alert('Họ tên chỉ được chứa chữ!');</script>";
    } elseif (!preg_match('/^NV[0-9]+$/', $ma_nv)) {
        echo "<script>alert('Mã nhân viên phải bắt đầu bằng NV và theo sau là số!');</script>";
    } elseif ($nvDAO->existsByMaNv($ma_nv)) {
        echo "<script>alert('Mã nhân viên đã tồn tại!');</script>";
    } else {

        $tuoi = date_diff(date_create($ngay_sinh), date_create())->y;
        if ($tuoi < 18 || $tuoi > 65) {
            echo "<script>alert('Tuổi nhân viên phải từ 18 đến 65!');</script>";
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
                echo "<script>alert('Thêm nhân viên thành công!'); location.href='hosonhanvien.php';</script>";
            } else {
                echo "<script>alert('Lỗi: Không thể thêm nhân viên!');</script>";
            }
        }
    }
}

// ====================== CHUẨN BỊ DỮ LIỆU SỬA ======================
$edit_state = false;

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
    $edit_ma_khoa   = $nv_edit['ma_khoa']; // lấy từ DB
}

// ====================== CẬP NHẬT NHÂN VIÊN ======================
if (isset($_POST['capnhat'])) {

    $ma_nv     = trim($_POST['ma_nv']);
    $ho_ten    = trim($_POST['ho_ten']);
    $ngay_sinh = trim($_POST['ngay_sinh']);
    $gioi_tinh = trim($_POST['gioi_tinh']);
    $chuc_vu   = trim($_POST['chuc_vu']);
    $user_id   = trim($_POST['user_id']);
    $ma_khoa   = $_POST['ma_khoa'] ?? $ma_khoa_session;

    $tuoi = date_diff(date_create($ngay_sinh), date_create())->y;

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
            echo "<script>alert('Cập nhật thành công!'); location.href='hosonhanvien.php';</script>";
        } else {
            echo "<script>alert('Lỗi: Không thể cập nhật nhân viên!');</script>";
        }
    }
}

// ====================== XÓA NHÂN VIÊN ======================
if (isset($_GET['delete'])) {
    $ma_nv_delete = $_GET['delete'];
    if ($nvDAO->delete($ma_nv_delete)) {
        echo "<script>alert('Xóa thành công!'); location.href='hosonhanvien.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa nhân viên!');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ nhân viên</title>
    <link rel="stylesheet" href="style.css">
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
            <h3><?= $edit_state ? 'Sửa nhân viên' : 'Thêm nhân viên'; ?></h3>

            <form method="post">

                <label>Mã NV:</label>
                <input placeholder="Ví dụ: NV01" type="text" name="ma_nv" required 
                       value="<?= e($edit_state ? $edit_ma_nv : "") ?>"
                       <?= $edit_state ? "readonly" : "" ?>>

                <?php if ($edit_state): ?>
                    <label>User ID:</label>
                    <input type="text" value="<?= e($edit_user_id) ?>" disabled>
                    <input type="hidden" name="user_id" value="<?= e($edit_user_id) ?>">
                <?php endif; ?>

                <label>Họ tên:</label>
                <input placeholder="Ví dụ: Lê Văn A" type="text" name="ho_ten" 
                       required value="<?= e($edit_ho_ten ?? "") ?>">

                <label>Ngày sinh:</label>
                <input type="date" name="ngay_sinh" required value="<?= e($edit_ngay_sinh ?? "") ?>">

                <label>Giới tính:</label>
                <select name="gioi_tinh">
                    <option value="Nam" <?= ($edit_gioi_tinh ?? "") == "Nam" ? "selected" : ""; ?>>Nam</option>
                    <option value="Nu" <?= ($edit_gioi_tinh ?? "") == "Nu"  ? "selected" : ""; ?>>Nữ</option>
                </select>

              <label>Chức vụ:</label>
<select name="chuc_vu" required>
    <option value="">-- Chọn chức vụ --</option>

    <option value="Nhân viên"
        <?= (isset($edit_chuc_vu) && $edit_chuc_vu === "Nhân viên") ? "selected" : "" ?>>
        Nhân viên
    </option>

    <option value="Lãnh đạo khoa"
        <?= (isset($edit_chuc_vu) && $edit_chuc_vu === "Lãnh đạo khoa") ? "selected" : "" ?>>
        Lãnh đạo khoa
    </option>
</select>

                <!-- MÃ KHOA -->
                <label>Mã khoa:</label>
                <input type="text" value="<?= e($edit_state ? $edit_ma_khoa : $ma_khoa_session) ?>" disabled>
                <input type="hidden" name="ma_khoa" 
                       value="<?= e($edit_state ? $edit_ma_khoa : $ma_khoa_session) ?>">

                <button name="<?= $edit_state ? 'capnhat' : 'them'; ?>">
                    <?= $edit_state ? 'Cập nhật' : 'Thêm'; ?>
                </button>

            </form>
        </div>

        <br>

        <!-- FORM TÌM KIẾM -->
        <form method="GET">
            <input type="text" name="search" placeholder="Tìm theo mã hoặc tên" 
                   value="<?= e($search) ?>">
            <button type="submit" name="search_btn">Tìm kiếm</button>
        </form>

        <!-- DANH SÁCH NHÂN VIÊN -->
        <div class="table-box">
            <h3>Danh sách nhân viên</h3>

            <table>
                <tr>
                    <th>Mã NV</th>
                    <th>User ID</th>
                    <th>Họ tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Chức vụ</th>
                    <th>Mã khoa</th>
                    <th>Thao tác</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= e($row['ma_nv']) ?></td>
                        <td><?= e($row['user_id']) ?></td>
                        <td><?= e($row['ho_ten']) ?></td>
                        <td><?= e($row['ngay_sinh']) ?></td>
                        <td><?= e($row['gioi_tinh']) ?></td>
                        <td><?= e($row['chuc_vu']) ?></td>
                        <td><?= e($row['ma_khoa']) ?></td>
                        <td class="actions">
                            <a href="hosonhanvien.php?edit=<?= e($row['ma_nv']) ?>" class="btn-edit">Sửa</a>
                            <a href="hosonhanvien.php?delete=<?= e($row['ma_nv']) ?>" 
                               class="btn-delete"
                               onclick="return confirm('Xóa nhân viên <?= e($row['ma_nv']) ?>?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </table>
        </div>

    </div>

</body>

</html>
