<?php

// Lớp NhanVien biểu diễn 1 nhân viên trong hệ thống
class NhanVien
{
    // Thuộc tính trùng với bảng nhân viên trong CSDL
    private $ma_nv;
    private $user_id;
    private $ho_ten;
    private $ngay_sinh;
    private $gioi_tinh;
    private $chuc_vu;
    private $ma_khoa;

    // Hàm khởi tạo đối tượng NhanVien (maps row từ DB)
    public function __construct($row)
    {
        $this->ma_nv     = $row['ma_nv']     ?? null;
        $this->user_id   = $row['user_id']   ?? null;
        $this->ho_ten    = $row['ho_ten']    ?? null;
        $this->ngay_sinh = $row['ngay_sinh'] ?? null;
        $this->gioi_tinh = $row['gioi_tinh'] ?? null;
        $this->chuc_vu   = $row['chuc_vu']   ?? null;
        $this->ma_khoa   = $row['ma_khoa']   ?? null;
    }

    // Getter & Setter cho từng thuộc tính
    public function getMaNv()
     { 
        return $this->ma_nv; 
    }
    public function setMaNv($ma_nv) 
    { 
        $this->ma_nv = $ma_nv;
     }

    public function getUserId() 
    {
         return $this->user_id; 
    }
    public function setUserId($user_id) 
    {
         $this->user_id = $user_id; 
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

    public function getChucVu() 
    { 
        return $this->chuc_vu; 
    }
    public function setChucVu($chuc_vu) 
    {
         $this->chuc_vu = $chuc_vu; 
    }

    public function getMaKhoa() 
    {
         return $this->ma_khoa;
     }
    public function setMaKhoa($ma_khoa) 
    {
          $this->ma_khoa = $ma_khoa; 
    }
}
