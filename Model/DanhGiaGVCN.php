<?php
class DanhGiaGVCN
{
    private $id;
    private $ma_sv;
    private $diem_ren_luyen;
    private $nhan_xet;
    private $ngay;
    private $nam_hoc;
    private $hoc_ky;
    private $gv_chu_nhiem;

    public function __construct($id, $ma_sv, $diem_ren_luyen, $nhan_xet, $nam_hoc, $hoc_ky, $ngay, $gv_chu_nhiem ) // Bỏ $id=null ở đây
    {
        $this->id = $id;
        $this->ma_sv = $ma_sv;
        $this->diem_ren_luyen = $diem_ren_luyen;
        $this->nhan_xet = $nhan_xet;
        $this->nam_hoc = $nam_hoc;
        $this->hoc_ky = $hoc_ky;
        $this->ngay = $ngay; 
        $this->gv_chu_nhiem = $gv_chu_nhiem;
    }

    public function getId() { return $this->id; }
    public function getMaSv() { return $this->ma_sv; }
    public function getDiemRL() { return $this->diem_ren_luyen; }
    public function getNhanXet() { return $this->nhan_xet; }
    public function getNgay() { return $this->ngay; }
    public function getNamHoc() { return $this->nam_hoc; }
    public function getHocKy() { return $this->hoc_ky; }

    public function setDiemRL($value){ $this->diem_ren_luyen = $value; }
    public function setNhanXet($value){ $this->nhan_xet = $value; }
    public function setNgay($value){ $this->ngay = $value; }
    public function getGVChuNhiem() { return $this->gv_chu_nhiem; }
}