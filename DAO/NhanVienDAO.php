<?php
// DAO làm việc với bảng nhanvien trong CSDL
require_once __DIR__ . '/../Model/NhanVien.php';

class NhanVienDAO
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Kiểm tra mã nhân viên tồn tại chưa
    public function existsByMaNv($ma_nv)
    {
        $ma_nv = mysqli_real_escape_string($this->conn, $ma_nv);
        $sql = "SELECT ma_nv FROM nhanvien WHERE ma_nv = '$ma_nv'";
        $result = mysqli_query($this->conn, $sql);
        if (!$result) return false;
        return mysqli_num_rows($result) > 0;
    }

    // Thêm nhân viên mới
    public function insert(NhanVien $nv)
    {
        $ma_nv   = mysqli_real_escape_string($this->conn, $nv->getMaNv());
        $user_id = mysqli_real_escape_string($this->conn, $nv->getUserId());
        $ho_ten  = mysqli_real_escape_string($this->conn, $nv->getHoTen());
        $ngay_sinh = mysqli_real_escape_string($this->conn, $nv->getNgaySinh());
        $gioi_tinh = mysqli_real_escape_string($this->conn, $nv->getGioiTinh());
        $chuc_vu   = mysqli_real_escape_string($this->conn, $nv->getChucVu());
        $ma_khoa   = mysqli_real_escape_string($this->conn, $nv->getMaKhoa());

        $sql = "INSERT INTO nhanvien(ma_nv, user_id, ho_ten, ngay_sinh, gioi_tinh, chuc_vu, ma_khoa)
                VALUES ('$ma_nv', '$user_id', '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$chuc_vu', '$ma_khoa')";

        return mysqli_query($this->conn, $sql);
    }

    // Lấy nhân viên theo mã
    public function getByMaNv($ma_nv)
    {
        $ma_nv = mysqli_real_escape_string($this->conn, $ma_nv);
        $sql = "SELECT * FROM nhanvien WHERE ma_nv = '$ma_nv'";
        return mysqli_query($this->conn, $sql);
    }

    // Cập nhật nhân viên
    public function update(NhanVien $nv)
    {
        $ma_nv   = mysqli_real_escape_string($this->conn, $nv->getMaNv());
        $user_id = mysqli_real_escape_string($this->conn, $nv->getUserId());
        $ho_ten  = mysqli_real_escape_string($this->conn, $nv->getHoTen());
        $ngay_sinh = mysqli_real_escape_string($this->conn, $nv->getNgaySinh());
        $gioi_tinh = mysqli_real_escape_string($this->conn, $nv->getGioiTinh());
        $chuc_vu   = mysqli_real_escape_string($this->conn, $nv->getChucVu());
        $ma_khoa   = mysqli_real_escape_string($this->conn, $nv->getMaKhoa());

        $sql = "UPDATE nhanvien 
                SET user_id='$user_id', ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', chuc_vu='$chuc_vu', ma_khoa='$ma_khoa'
                WHERE ma_nv='$ma_nv'";

        return mysqli_query($this->conn, $sql);
    }

    // Xóa nhân viên theo mã
    public function delete($ma_nv)
    {
        $ma_nv = mysqli_real_escape_string($this->conn, $ma_nv);
        $sql = "DELETE FROM nhanvien WHERE ma_nv='$ma_nv'";
        return mysqli_query($this->conn, $sql);
    }

    // Lấy tất cả nhân viên theo mã khoa
    public function getAllByKhoa($ma_khoa)
    {
        $ma_khoa = mysqli_real_escape_string($this->conn, $ma_khoa);
        $sql = "SELECT * FROM nhanvien WHERE ma_khoa='$ma_khoa'";
        return mysqli_query($this->conn, $sql);
    }

    // Tìm kiếm nhân viên theo từ khóa (mã hoặc họ tên)
    public function search($keyword, $ma_khoa)
    {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        $ma_khoa = mysqli_real_escape_string($this->conn, $ma_khoa);
        $sql = "SELECT * FROM nhanvien 
                WHERE ma_khoa='$ma_khoa' 
                AND (ma_nv LIKE '%$keyword%' OR ho_ten LIKE '%$keyword%')";
        return mysqli_query($this->conn, $sql);
    }
}
?>
