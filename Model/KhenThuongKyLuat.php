<?php

// Lớp KhenThuongKyLuat đại diện cho 1 bản ghi khen thưởng / kỷ luật
class KhenThuongKyLuat
{
    private $id;
    private $ma_nv;
    private $loai;
    private $cap_quan_ly;
    private $noi_dung;
    private $ngay;
    private $nguoi_duyet;

    // Khởi tạo từ 1 bản ghi lấy từ CSDL
    public function __construct($row)
    {
        $this->id           = $row['id']           ?? null;
        $this->ma_nv        = $row['ma_nv']        ?? null;
        $this->loai         = $row['loai']         ?? null;
        $this->cap_quan_ly  = $row['cap_quan_ly']  ?? null;
        $this->noi_dung     = $row['noi_dung']     ?? null;
        $this->ngay         = $row['ngay']         ?? null;
        $this->nguoi_duyet  = $row['nguoi_duyet']  ?? null;
    }

    // Getter & Setter
    public function getId() 
    {
         return $this->id; 
    }
    public function setId($id) 
    {
         $this->id = $id; 
    }

    public function getMaNv() 
    {
         return $this->ma_nv; 
    }
    public function setMaNv($ma_nv) 
    {
         $this->ma_nv = $ma_nv; 
    }

    public function getLoai() 
    {
         return $this->loai;
    }
    public function setLoai($loai) 
    {
         $this->loai = $loai; 
    }

    public function getCapQuanLy() 
    {
         return $this->cap_quan_ly; 
    }
    public function setCapQuanLy($cap_quan_ly) 
    {
         $this->cap_quan_ly = $cap_quan_ly;
    }

    public function getNoiDung() 
    {
         return $this->noi_dung; 
    }
    public function setNoiDung($noi_dung) 
    {
         $this->noi_dung = $noi_dung; 
    }

    public function getNgay() 
    {
         return $this->ngay; 
    }
    public function setNgay($ngay) 
    {
         $this->ngay = $ngay; 
    }

    public function getNguoiDuyet() 
    {
         return $this->nguoi_duyet; 
    }
    public function setNguoiDuyet($nguoi_duyet) 
    {
         $this->nguoi_duyet = $nguoi_duyet; 
    }
}
