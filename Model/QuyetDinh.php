<?php
class QuyetDinh {
    private $id;
    private $ma_sv;
    private $loai;
    private $cap_quan_ly;
    private $noi_dung;
    private $ngay;
    private $nguoi_duyet;

    public function __construct($data = array()) {
        $this->id = $data['id'] ?? null;
        $this->ma_sv = $data['ma_sv'] ?? '';
        $this->loai = $data['loai'] ?? '';
        $this->cap_quan_ly = $data['cap_quan_ly'] ?? '';
        $this->noi_dung = $data['noi_dung'] ?? '';
        $this->ngay = $data['ngay'] ?? '';
        $this->nguoi_duyet = $data['nguoi_duyet'] ?? null;
    }

    // Getter & Setter
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function getMaSv() {
        return $this->ma_sv;
    }
    
    public function setMaSv($ma_sv) {
        $this->ma_sv = $ma_sv;
    }

    public function getLoai() {
        return $this->loai;
    }
    
    public function setLoai($loai) {
        $this->loai = $loai;
    }

    public function getCapQuanLy() {
        return $this->cap_quan_ly;
    }
    
    public function setCapQuanLy($cap_quan_ly) {
        $this->cap_quan_ly = $cap_quan_ly;
    }

    public function getNoiDung() {
        return $this->noi_dung;
    }
    
    public function setNoiDung($noi_dung) {
        $this->noi_dung = $noi_dung;
    }

    public function getNgay() {
        return $this->ngay;
    }
    
    public function setNgay($ngay) {
        $this->ngay = $ngay;
    }

    public function getNguoiDuyet() {
        return $this->nguoi_duyet;
    }
    
    public function setNguoiDuyet($nguoi_duyet) {
        $this->nguoi_duyet = $nguoi_duyet;
    }

    // Phương thức tiện ích
    public function isKhenThuong() {
        return $this->loai === 'Khen thuong';
    }

    public function isKyLuat() {
        return $this->loai === 'Ky luat';
    }

    public function getLoaiText() {
        return $this->isKhenThuong() ? 'Khen thưởng' : 'Kỷ luật';
    }
}
?>