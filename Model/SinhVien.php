<?php

// Lớp SinhVien biểu diễn 1 đối tượng sinh viên trong hệ thống
class SinhVien
{
    // Thuộc tính trùng với bảng SinhVien trong CSDL
    private $ma_sv;
    private $ho_ten;
    private $ngay_sinh;
    private $gioi_tinh;
    private $ma_lop;
    private $ma_khoa;

    // Hàm khởi tạo đối tượng SinhVien
    public function __construct($ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $ma_lop, $ma_khoa)
    {
        $this->ma_sv     = $ma_sv;
        $this->ho_ten    = $ho_ten;
        $this->ngay_sinh = $ngay_sinh;
        $this->gioi_tinh = $gioi_tinh;
        $this->ma_lop    = $ma_lop;
        $this->ma_khoa   = $ma_khoa;
    }


    // Getter & Setter cho từng thuộc tính
    public function getMaSv()
    {
        return $this->ma_sv;
    }
    public function setMaSv($ma_sv)
    {
        $this->ma_sv = $ma_sv;
    }

    public function getHoTen()
    {
        return $this->ho_ten;
    }
    public function setHoTen($ho_ten)
    {
        $this->ho_ten = $ho_ten;
    }

    public function getNgaySinh()
    {
        return $this->ngay_sinh;
    }
    public function setNgaySinh($ngay_sinh)
    {
        $this->ngay_sinh = $ngay_sinh;
    }

    public function getGioiTinh()
    {
        return $this->gioi_tinh;
    }
    public function setGioiTinh($gioi_tinh)
    {
        $this->gioi_tinh = $gioi_tinh;
    }

    public function getMaLop()
    {
        return $this->ma_lop;
    }
    public function setMaLop($ma_lop)
    {
        $this->ma_lop = $ma_lop;
    }

    public function getMaKhoa()
    {
        return $this->ma_khoa;
    }
    public function setMaKhoa($ma_khoa)
    {
        $this->ma_khoa = $ma_khoa;
    }


    //
}