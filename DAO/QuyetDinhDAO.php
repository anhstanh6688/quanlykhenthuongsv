<?php
class QuyetDinhDAO {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getQuyetDinhByKhoa($ma_khoa, $search = '') {
        $ma_khoa = mysqli_real_escape_string($this->conn, $ma_khoa);
        $search = mysqli_real_escape_string($this->conn, $search);
        
        $sql = "SELECT kt.*, sv.ho_ten, sv.ma_sv, nd.username as nguoi_duyet_ten
               FROM khenthuongkyluat kt 
               JOIN sinhvien sv ON kt.ma_sv = sv.ma_sv 
               LEFT JOIN nguoidung nd ON kt.nguoi_duyet = nd.user_id
               WHERE sv.ma_khoa = '$ma_khoa'";

        if (!empty($search)) {
            $sql .= " AND (sv.ma_sv LIKE '%$search%' 
                          OR sv.ho_ten LIKE '%$search%' 
                          OR kt.loai LIKE '%$search%'
                          OR kt.noi_dung LIKE '%$search%'
                          OR kt.cap_quan_ly LIKE '%$search%')";
        }

        // Sắp xếp theo ngày giảm dần (mới nhất lên đầu) và theo ID giảm dần
        $sql .= " ORDER BY kt.ngay DESC, kt.id DESC";

        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [];
        }
        
        $quyetdinhs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $quyetdinhs[] = $row;
        }

        return $quyetdinhs;
    }

    public function getQuyetDinhById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $sql = "SELECT kt.*, sv.ho_ten, sv.ma_sv, nd.username as nguoi_duyet_ten
                FROM khenthuongkyluat kt 
                JOIN sinhvien sv ON kt.ma_sv = sv.ma_sv 
                LEFT JOIN nguoidung nd ON kt.nguoi_duyet = nd.user_id
                WHERE kt.id = '$id'";
        $result = mysqli_query($this->conn, $sql);
        
        return ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : null;
    }

    public function themQuyetDinh($ma_sv, $loai, $cap_quan_ly, $noi_dung, $ngay, $nguoi_duyet) {
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);
        $loai = mysqli_real_escape_string($this->conn, $loai);
        $cap_quan_ly = mysqli_real_escape_string($this->conn, $cap_quan_ly);
        $noi_dung = mysqli_real_escape_string($this->conn, $noi_dung);
        $ngay = mysqli_real_escape_string($this->conn, $ngay);
        $nguoi_duyet = mysqli_real_escape_string($this->conn, $nguoi_duyet);

        $sql = "INSERT INTO khenthuongkyluat (ma_sv, loai, cap_quan_ly, noi_dung, ngay, nguoi_duyet) 
                VALUES ('$ma_sv', '$loai', '$cap_quan_ly', '$noi_dung', '$ngay', '$nguoi_duyet')";
        
        return mysqli_query($this->conn, $sql);
    }

    public function suaQuyetDinh($id, $ma_sv, $loai, $cap_quan_ly, $noi_dung, $ngay) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);
        $loai = mysqli_real_escape_string($this->conn, $loai);
        $cap_quan_ly = mysqli_real_escape_string($this->conn, $cap_quan_ly);
        $noi_dung = mysqli_real_escape_string($this->conn, $noi_dung);
        $ngay = mysqli_real_escape_string($this->conn, $ngay);

        $sql = "UPDATE khenthuongkyluat 
                SET ma_sv='$ma_sv', loai='$loai', cap_quan_ly='$cap_quan_ly', 
                    noi_dung='$noi_dung', ngay='$ngay' 
                WHERE id='$id'";
        
        return mysqli_query($this->conn, $sql);
    }

    public function xoaQuyetDinh($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $sql = "DELETE FROM khenthuongkyluat WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>