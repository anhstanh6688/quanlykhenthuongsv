<?php
// BẬT HIỂN THỊ LỖI - CHỈ DÙNG KHI DEVELOP
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../auth.php";
include "../DB/connect.php"; 
include "../DAO/SinhVienDAO.php";  
include "../DAO/QuyetDinhDAO.php"; 

// Khởi tạo DAO
$sinhVienDAO = new SinhVienDAO($conn);
$quyetDinhDAO = new QuyetDinhDAO($conn);

// Lưu mã khoa từ tài khoản đăng nhập
$ma_khoa = $_SESSION['ma_khoa'];

// Khởi tạo biến tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xử lý thêm quyết định
if (isset($_POST['them'])) {
    $ma_sv = $_POST['ma_sv'];
    $loai = $_POST['loai'];
    $cap_quan_ly = $_POST['cap_quan_ly'];
    $noi_dung = $_POST['noi_dung'];
    $ngay = $_POST['ngay'];
    $nguoi_duyet = $_SESSION['user_id'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($ma_sv) || empty($loai) || empty($cap_quan_ly) || empty($noi_dung) || empty($ngay)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } else {
        if (!$sinhVienDAO->existsByMaSv($ma_sv)) {
            echo "<script>alert('Mã sinh viên không tồn tại trong hệ thống!');</script>";
        } else {
            // Kiểm tra thêm: sinh viên có thuộc khoa của lãnh đạo không
            // Lấy thông tin sinh viên để kiểm tra
            $result = $sinhVienDAO->getByMaSv($ma_sv);
            if ($result && mysqli_num_rows($result) > 0) {
                $sinhVien = mysqli_fetch_assoc($result);
                if ($sinhVien['ma_khoa'] != $ma_khoa) {
                    echo "<script>alert('Mã sinh viên không thuộc khoa của bạn!');</script>";
                } else {
                    if ($quyetDinhDAO->themQuyetDinh($ma_sv, $loai, $cap_quan_ly, $noi_dung, $ngay, $nguoi_duyet)) {
                        header("Location: ld_khoa.php");
                        exit();
                    } else {
                        echo "<script>alert('Lỗi khi thêm quyết định!');</script>";
                    }
                }
            } else {
                echo "<script>alert('Không tìm thấy thông tin sinh viên!');</script>";
            }
        }
    }
}

// Xử lý sửa quyết định
$edit_state = false;
$edit_id = '';
$edit_ma_sv = '';
$edit_loai = '';
$edit_cap_quan_ly = '';
$edit_noi_dung = '';
$edit_ngay = '';

if (isset($_GET['edit'])) {
    $edit_state = true;
    $id = $_GET['edit'];
    $quyetDinh = $quyetDinhDAO->getQuyetDinhById($id); 
    
    if ($quyetDinh) {
        $edit_id = $quyetDinh['id'];
        $edit_ma_sv = $quyetDinh['ma_sv'];
        $edit_loai = $quyetDinh['loai'];
        $edit_cap_quan_ly = $quyetDinh['cap_quan_ly'];
        $edit_noi_dung = $quyetDinh['noi_dung'];
        $edit_ngay = $quyetDinh['ngay'];
    } else {
        echo "<script>alert('Không tìm thấy quyết định!');</script>";
        header("Location: ld_khoa.php");
        exit();
    }
}

// Xử lý cập nhật quyết định
if (isset($_POST['capnhat'])) {
    $id = $_POST['id'];
    $ma_sv = $_POST['ma_sv'];
    $loai = $_POST['loai'];
    $cap_quan_ly = $_POST['cap_quan_ly'];
    $noi_dung = $_POST['noi_dung'];
    $ngay = $_POST['ngay'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($ma_sv) || empty($loai) || empty($cap_quan_ly) || empty($noi_dung) || empty($ngay)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } else {
        if (!$sinhVienDAO->existsByMaSv($ma_sv)) {
            echo "<script>alert('Mã sinh viên không tồn tại trong hệ thống!');</script>";
        } else {
            // Kiểm tra thêm: sinh viên có thuộc khoa của lãnh đạo không
            $result = $sinhVienDAO->getByMaSv($ma_sv);
            if ($result && mysqli_num_rows($result) > 0) {
                $sinhVien = mysqli_fetch_assoc($result);
                if ($sinhVien['ma_khoa'] != $ma_khoa) {
                    echo "<script>alert('Mã sinh viên không thuộc khoa của bạn!');</script>";
                } else {
                    if ($quyetDinhDAO->suaQuyetDinh($id, $ma_sv, $loai, $cap_quan_ly, $noi_dung, $ngay)) {
                        header("Location: ld_khoa.php");
                        exit();
                    } else {
                        echo "<script>alert('Lỗi khi cập nhật quyết định!');</script>";
                    }
                }
            } else {
                echo "<script>alert('Không tìm thấy thông tin sinh viên!');</script>";
            }
        }
    }
}

// Xử lý xóa quyết định
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    if ($quyetDinhDAO->xoaQuyetDinh($id)) {
        header("Location: ld_khoa.php");
        exit();
    } else {
        echo "<script>alert('Lỗi khi xóa quyết định!');</script>";
    }
}

// Lấy danh sách sinh viên 
if (!empty($search)) {
    $result_sv = $sinhVienDAO->search($search, $ma_khoa);
} else {
    $result_sv = $sinhVienDAO->getAllByKhoa($ma_khoa);
}

$danhSachSinhVien = [];
if ($result_sv && mysqli_num_rows($result_sv) > 0) {
    while ($row = mysqli_fetch_assoc($result_sv)) {
        $danhSachSinhVien[] = $row;
    }
}

/// Lấy danh sách quyết định
$danhSachQuyetDinh = $quyetDinhDAO->getQuyetDinhByKhoa($ma_khoa, $search);

// Sắp xếp từ mới nhất đến cũ nhất
if (!empty($danhSachQuyetDinh)) {
    usort($danhSachQuyetDinh, function($a, $b) {
        return strtotime($b['ngay']) - strtotime($a['ngay']);
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../LDKhoa/stylesldkhoa.css">
    <title>Trang lãnh đạo khoa</title>
    <style>
        /* Thêm style cho autocomplete */
        .autocomplete-container {
            position: relative;
            width: 100%;
        }
        
        .autocomplete-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d7dce3;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .autocomplete-suggestion {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .autocomplete-suggestion:hover {
            background-color: #f7fbff;
        }
        
        .autocomplete-suggestion.active {
            background-color: #6ab7ff;
            color: white;
        }
        
        /* Thêm style cho form validation */
        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 4px;
            display: none;
        }
        
        .valid-input {
            border-color: #2ecc71 !important;
        }
        
        .invalid-input {
            border-color: #e74c3c !important;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header-bar">
        <h2>Quản lý quyết định khen thưởng - Lãnh đạo khoa</h2>
        <a class="logout" href="../logout.php">Đăng xuất</a>
    </div>

    <div class="container">
        <!-- FORM THÊM/SỬA QUYẾT ĐỊNH -->
        <div class="form-box">
            <h3><?php echo $edit_state ? 'Sửa quyết định' : 'Thêm quyết định'; ?></h3>
            <form method="post" id="formQuyetDinh" onsubmit="return validateForm()">
                <!-- ID (ẩn) -->
                <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                
                <!-- Số quyết định (hiển thị ID khi sửa) -->
                <?php if ($edit_state) { ?>
                <label>Số quyết định:</label>
                <input type="text" value="<?php echo $edit_id; ?>" disabled style="background: #f5f5f5;">
                <small style="color: #666;">Số quyết định không thể thay đổi</small>
                <?php } ?>

                <!-- Mã sinh viên -->
                <div class="autocomplete-container">
                    <label>Mã SV: <span id="ma_sv_status"></span></label>
                    <input type="text" placeholder="Nhập mã sinh viên. Ví dụ SV01" name="ma_sv" 
                           id="ma_sv_input" required <?php echo $edit_state ? 'readonly' : ''; ?>
                           value="<?php echo htmlspecialchars($edit_ma_sv); ?>"
                           autocomplete="off"
                           onblur="checkMaSV(this.value)"
                           class="<?php echo $edit_state ? '' : 'checkable-input'; ?>">
                    <div class="autocomplete-suggestions" id="suggestions"></div>
                    <small style="color: #666; display: block; margin-top: 4px;" id="ma_sv_hint">
                        Gợi ý: <?php 
                            if (count($danhSachSinhVien) > 0) {
                                echo implode(', ', array_slice(array_column($danhSachSinhVien, 'ma_sv'), 0, 3));
                                if (count($danhSachSinhVien) > 3) echo ', ...';
                            } else {
                                echo 'Chưa có sinh viên nào';
                            }
                        ?>
                    </small>
                    <div class="error-message" id="ma_sv_error"></div>
                </div>

                <!-- Loại quyết định -->
                <label>Loại quyết định:</label>
                <select name="loai" required id="loai_select">
                    <option value="">-- Chọn loại quyết định --</option>
                    <option value="Khen thuong" <?php if ($edit_loai == 'Khen thuong') echo 'selected'; ?>>Khen thưởng</option>
                    <option value="Ky luat" <?php if ($edit_loai == 'Ky luat') echo 'selected'; ?>>Kỷ luật</option>
                </select>

                <!-- Cấp quản lý -->
                <label>Cấp quản lý:</label>
                <select name="cap_quan_ly" required id="cap_quan_ly_select">
                    <option value="">-- Chọn cấp quản lý --</option>
                    <option value="Khoa/Vien" <?php if ($edit_cap_quan_ly == 'Khoa/Vien') echo 'selected'; ?>>Khoa/Viện</option>
                    <option value="Truong" <?php if ($edit_cap_quan_ly == 'Truong') echo 'selected'; ?>>Trường</option>
                </select>

                <!-- Người duyệt (ẩn, lấy từ session) -->
                <input type="hidden" name="nguoi_duyet" value="<?php echo $_SESSION['user_id']; ?>">

                <!-- Nội dung --> 
                <label>Nội dung: </label>
                <textarea name="noi_dung" placeholder="Nhập nội dung" rows="3" required id="noi_dung_textarea"><?php echo htmlspecialchars($edit_noi_dung); ?></textarea>

                <!-- Ngày -->
                <label>Ngày: </label>
                <input type="date" name="ngay" required value="<?php echo $edit_ngay; ?>" id="ngay_input">

                <!-- Nút chức năng -->
                <?php if ($edit_state == false) { ?>
                    <button type="submit" name="them" id="submit_button">Thêm quyết định</button>
                <?php } else { ?>
                    <button type="submit" name="capnhat" id="submit_button">Cập nhật</button>
                    <a href="ld_khoa.php" class="btn-cancel">Hủy</a>
                <?php } ?>
            </form>
        </div>

        <!-- FORM TÌM KIẾM -->
        <div class="search-box">
            <h3>Tìm kiếm sinh viên và quyết định</h3>
            <form method="GET" action="">
                <div class="search-form-group">
                    <input type="text" name="search" placeholder="Tìm kiếm theo mã SV, họ tên hoặc loại quyết định"
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Tìm kiếm</button>
                    <?php if ($search != '') { ?>
                        <a href="ld_khoa.php" class="clear-search">Xóa tìm kiếm</a>
                    <?php } ?>
                </div>
            </form>
        </div>

        <!-- HIỂN THỊ DANH SÁCH SINH VIÊN (CHỈ KHI CÓ TÌM KIẾM) -->
        <?php if ($search != '') { ?>
        <div class="table-box">
            <h3>Kết Quả Tìm Kiếm Sinh Viên</h3>
            <?php if (count($danhSachSinhVien) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Lớp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachSinhVien as $sv) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sv['ma_sv']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ngay_sinh']); ?></td>
                                <td><?php echo htmlspecialchars($sv['gioi_tinh']); ?></td>
                                <td><?php echo htmlspecialchars($sv['ten_lop']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p style="text-align: center; color: #666;">Không tìm thấy sinh viên nào phù hợp</p>
            <?php } ?>
        </div>
        <?php } ?>

        <!-- HIỂN THỊ DANH SÁCH QUYẾT ĐỊNH -->
        <div class="table-box">
            <h3>Danh Sách Quyết Định <?php echo $search != '' ? '(Kết quả tìm kiếm)' : ''; ?></h3>
            <?php if (count($danhSachQuyetDinh) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Số QĐ</th>
                            <th>Mã SV</th>
                            <th>Họ Tên</th>
                            <th>Loại</th>
                            <th>Nội dung</th>
                            <th>Ngày</th>
                            <th>Cấp quản lý</th>
                            <th>Người phê duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachQuyetDinh as $qd) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($qd['id']); ?></td>
                                <td><?php echo htmlspecialchars($qd['ma_sv']); ?></td>
                                <td><?php echo htmlspecialchars($qd['ho_ten']); ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                        <?php echo $qd['loai'] == 'Khen thuong' ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;'; ?>">
                                        <?php echo $qd['loai'] == 'Khen thuong' ? 'Khen thưởng' : 'Kỷ luật'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($qd['noi_dung']); ?></td>
                                <td><?php echo htmlspecialchars($qd['ngay']); ?></td>
                                <td><?php echo htmlspecialchars($qd['cap_quan_ly']); ?></td>
                                <td><?php echo isset($qd['nguoi_duyet_ten']) ? htmlspecialchars($qd['nguoi_duyet_ten']) : 'N/A'; ?></td>
                                <td class="actions">
                                    <a href="ld_khoa.php?edit=<?php echo $qd['id']; ?>" class="btn-edit">Sửa</a>
                                    <a href="ld_khoa.php?delete=<?php echo $qd['id']; ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa quyết định này không?');">
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p style="text-align: center; color: #666;">
                    <?php echo $search != '' ? 'Không tìm thấy quyết định nào phù hợp' : 'Chưa có quyết định nào'; ?>
                </p>
            <?php } ?>
        </div>
    </div>

    <script>
        // Dữ liệu gợi ý sinh viên
        const sinhVienData = <?php echo json_encode($danhSachSinhVien); ?>;
        
        // Lấy các phần tử DOM
        const maSvInput = document.getElementById('ma_sv_input');
        const suggestionsBox = document.getElementById('suggestions');
        const maSvStatus = document.getElementById('ma_sv_status');
        const maSvError = document.getElementById('ma_sv_error');
        const maSvHint = document.getElementById('ma_sv_hint');
        const submitButton = document.getElementById('submit_button');
        
        // Kiểm tra mã sinh viên 
function checkMaSV(ma_sv) {
    if (!ma_sv || ma_sv.trim() === '') {
        maSvStatus.innerHTML = '';
    } else {
        maSvStatus.innerHTML = '<span style="color: #3498db;">Đã nhập</span>';
    }
    maSvError.style.display = 'none';
    maSvInput.classList.remove('valid-input', 'invalid-input');
}
        
        // Hàm hiển thị gợi ý
        function showSuggestions(searchTerm) {
            if (!searchTerm || searchTerm.length < 1) {
                suggestionsBox.style.display = 'none';
                return;
            }
            
            const filtered = sinhVienData.filter(sv => 
                sv.ma_sv.toLowerCase().includes(searchTerm.toLowerCase()) || 
                sv.ho_ten.toLowerCase().includes(searchTerm.toLowerCase())
            );
            
            if (filtered.length === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }
            
            suggestionsBox.innerHTML = '';
            filtered.forEach((sv, index) => {
                const div = document.createElement('div');
                div.className = 'autocomplete-suggestion';
                div.innerHTML = `<strong>${sv.ma_sv}</strong> - ${sv.ho_ten} (${sv.ten_lop})`;
                div.dataset.value = sv.ma_sv;
                
                div.addEventListener('click', () => {
                    maSvInput.value = sv.ma_sv;
                    suggestionsBox.style.display = 'none';
                    checkMaSV(sv.ma_sv); // Kiểm tra ngay khi chọn
                });
                
                suggestionsBox.appendChild(div);
            });
            
            suggestionsBox.style.display = 'block';
        }
        
        // Xử lý sự kiện nhập liệu
        maSvInput.addEventListener('input', function() {
            showSuggestions(this.value);
            if (this.value.length >= 3) {
                checkMaSV(this.value);
            }
        });
        
        // Xử lý sự kiện bàn phím
        maSvInput.addEventListener('keydown', function(e) {
            const suggestions = suggestionsBox.querySelectorAll('.autocomplete-suggestion');
            const active = suggestionsBox.querySelector('.autocomplete-suggestion.active');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!active) {
                    suggestions[0]?.classList.add('active');
                } else {
                    active.classList.remove('active');
                    const next = active.nextElementSibling;
                    if (next) next.classList.add('active');
                    else suggestions[0]?.classList.add('active');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!active) {
                    suggestions[suggestions.length - 1]?.classList.add('active');
                } else {
                    active.classList.remove('active');
                    const prev = active.previousElementSibling;
                    if (prev) prev.classList.add('active');
                    else suggestions[suggestions.length - 1]?.classList.add('active');
                }
            } else if (e.key === 'Enter' && active) {
                e.preventDefault();
                maSvInput.value = active.dataset.value;
                suggestionsBox.style.display = 'none';
                checkMaSV(active.dataset.value);
            }
        });
        
        // Ẩn gợi ý khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.autocomplete-container')) {
                suggestionsBox.style.display = 'none';
            }
        });
        
        // Validate form
function validateForm() {
    // Kiểm tra mã sinh viên
    const ma_sv = maSvInput.value.trim();
    if (ma_sv === '') {
        alert('Vui lòng nhập mã sinh viên');
        maSvInput.focus();
        return false;
    }
    
    // Kiểm tra loại quyết định
    const loai = document.getElementById('loai_select').value;
    if (loai === '') {
        alert('Vui lòng chọn loại quyết định');
        document.getElementById('loai_select').focus();
        return false;
    }
    
    // Kiểm tra cấp quản lý
    const cap_quan_ly = document.getElementById('cap_quan_ly_select').value;
    if (cap_quan_ly === '') {
        alert('Vui lòng chọn cấp quản lý');
        document.getElementById('cap_quan_ly_select').focus();
        return false;
    }
    
    // Kiểm tra nội dung
    const noi_dung = document.getElementById('noi_dung_textarea').value.trim();
    if (noi_dung === '') {
        alert('Vui lòng nhập nội dung');
        document.getElementById('noi_dung_textarea').focus();
        return false;
    }
    
    // Kiểm tra ngày
    const ngay = document.getElementById('ngay_input').value;
    if (ngay === '') {
        alert('Vui lòng chọn ngày');
        document.getElementById('ngay_input').focus();
        return false;
    }
    
    return true;
}
        
        // Focus vào trường mã SV khi trang tải (chỉ khi không ở chế độ sửa)
        <?php if (!$edit_state) { ?>
        window.addEventListener('load', function() {
            if (!maSvInput.readOnly) {
                maSvInput.focus();
            }
        });
        <?php } ?>
    </script>
</body>

</html>