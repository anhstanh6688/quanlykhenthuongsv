<?php
// Lớp SinhVienDAO làm việc với bảng sinhvien trong CSDL

class SinhVienDAO
{
    // Biến kết nối CSDL
    private $conn;

    // Nhận kết nối từ bên ngoài truyền vào
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Kiểm tra xem mã sinh viên đã tồn tại chưa
    public function existsByMaSv($ma_sv)
    {
        // Chống SQL Injection
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);

        // Câu truy vấn kiểm tra
        $sql = "SELECT ma_sv FROM sinhvien WHERE ma_sv = '$ma_sv'";

        // Nếu lỗi truy vấn -> xem như chưa tồn tại
        $result = mysqli_query($this->conn, $sql);

        // Nếu lỗi truy vấn -> xem như chưa tồn tại
        if (!$result) {
            return false; // lỗi truy vấn thì tạm coi như chưa tồn tại
        }

        return mysqli_num_rows($result) > 0;
    }

    // THÊM MỚI SINH VIÊN
    public function insert(SinhVien $sv)
    {
        // Lấy dữ liệu từ đối tượng SinhVien
        $ma_sv     = mysqli_real_escape_string($this->conn, $sv->getMaSv());
        $ho_ten    = mysqli_real_escape_string($this->conn, $sv->getHoTen());
        $ngay_sinh = mysqli_real_escape_string($this->conn, $sv->getNgaySinh());
        $gioi_tinh = mysqli_real_escape_string($this->conn, $sv->getGioiTinh());
        $ma_lop    = mysqli_real_escape_string($this->conn, $sv->getMaLop());
        $ma_khoa   = mysqli_real_escape_string($this->conn, $sv->getMaKhoa());

        // Câu truy vấn thêm
        $sql = "INSERT INTO sinhvien(ma_sv, ho_ten, ngay_sinh, gioi_tinh, ma_lop, ma_khoa)
                VALUES ('$ma_sv', '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$ma_lop', '$ma_khoa')";

        // Trả về kết quả thực thi

        return mysqli_query($this->conn, $sql);
    }


    // Lấy thông tin chi tiết sinh viên theo mã
    public function getByMaSv($ma_sv)
    {
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);
        $sql = "SELECT * FROM sinhvien WHERE ma_sv = '$ma_sv'";
        return mysqli_query($this->conn, $sql);
    }

    // CẬP NHẬT THÔNG TIN SINH VIÊN
    public function update(SinhVien $sv)
    {
        $ma_sv     = mysqli_real_escape_string($this->conn, $sv->getMaSv());
        $ho_ten    = mysqli_real_escape_string($this->conn, $sv->getHoTen());
        $ngay_sinh = mysqli_real_escape_string($this->conn, $sv->getNgaySinh());
        $gioi_tinh = mysqli_real_escape_string($this->conn, $sv->getGioiTinh());
        $ma_lop    = mysqli_real_escape_string($this->conn, $sv->getMaLop());
        $ma_khoa   = mysqli_real_escape_string($this->conn, $sv->getMaKhoa());

        // Truy vấn cập nhật
        $sql = "UPDATE sinhvien 
            SET ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', 
                ma_lop='$ma_lop', ma_khoa='$ma_khoa'
            WHERE ma_sv='$ma_sv'";

        return mysqli_query($this->conn, $sql);
    }

    // XÓA SINH VIÊN THEO MÃ
    public function delete($ma_sv)
    {
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);

        // truy vấn xóa
        $sql = "DELETE FROM sinhvien WHERE ma_sv = '$ma_sv'";

        // trả về kết quả
        return mysqli_query($this->conn, $sql);
    }

    // LẤY TẤT CẢ SINH VIÊN THEO MÃ KHOA
    public function getAllByKhoa($ma_khoa)
    {
        $ma_khoa = mysqli_real_escape_string($this->conn, $ma_khoa);

        // truy vấn hiển thị sinh viên theo khoa
        $sql = "SELECT sv.ma_sv, sv.ho_ten, sv.ngay_sinh, sv.gioi_tinh, l.ten_lop
                FROM sinhvien sv
                JOIN lop l ON sv.ma_lop = l.ma_lop
                WHERE sv.ma_khoa = '$ma_khoa'";

        // trả về kết quả
        return mysqli_query($this->conn, $sql);
    }

    // TÌM KIẾM SINH VIÊN THEO TỪ KHÓA (mã hoặc họ tên)
    public function search($keyword, $ma_khoa)
    {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        $ma_khoa = mysqli_real_escape_string($this->conn, $ma_khoa);

        // thực hiện truy vấn danh sách theo keyword
        $sql = "SELECT sv.ma_sv, sv.ho_ten, sv.ngay_sinh, sv.gioi_tinh, l.ten_lop
            FROM sinhvien sv
            JOIN lop l ON sv.ma_lop = l.ma_lop
            WHERE sv.ma_khoa = '$ma_khoa'
            AND (sv.ma_sv LIKE '%$keyword%' OR sv.ho_ten LIKE '%$keyword%')";

        return mysqli_query($this->conn, $sql);
    }
}
