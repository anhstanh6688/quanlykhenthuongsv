<?php
class NguoiDungDAO
{
    // Thuộc tính kết nối CSDL
    private $conn;

    // HÀM KHỞI TẠO 
    // Nhận vào đối tượng kết nối $conn để dùng cho toàn lớp
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Hàm xác thực đăng nhập
    public function authenticate($username)
    {
        // Làm sạch chuỗi để chống SQL Injection
        $username = mysqli_real_escape_string($this->conn, $username);

        // Câu truy vấn tìm người dùng theo username
        $sql = "SELECT * FROM nguoidung WHERE username = '$username'";

        // Thực thi truy vấn
        $result = mysqli_query($this->conn, $sql);

        // Nếu truy vấn thành công và có dữ liệu
        if ($result && mysqli_num_rows($result) > 0) {
            include_once "./Model/NguoiDung.php";
            return new NguoiDung(mysqli_fetch_assoc($result));
        }

        return null; // Không tìm thấy user
    }
}
