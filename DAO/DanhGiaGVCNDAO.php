<?php
class DanhGiaGVCNDAO
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy đánh giá theo ID (để Sửa)
   public function getById($id)
{
    $sql = "SELECT * FROM danhgiagvcn WHERE id=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if(!$row) return null;

    return new DanhGiaGVCN(
        $row['id'],
        $row['ma_sv'],
        $row['diem_ren_luyen'],
        $row['nhan_xet'],
        $row['nam_hoc'],
        $row['hoc_ky'],
        $row['ngay'],
        $row['gv_chu_nhiem']
    );
}

    // Lấy đánh giá theo mã sinh viên (Xem đã đánh giá chưa)
    public function getByMaSv($ma_sv)
    {
        $ma_sv = mysqli_real_escape_string($this->conn, $ma_sv);
        $sql = "SELECT * FROM danhgiagvcn WHERE ma_sv = '$ma_sv'";
        return mysqli_query($this->conn, $sql);
    }

    // THÊM ĐÁNH GIÁ
    public function insert(DanhGiaGVCN $dg)
    {
        $ma_sv  = mysqli_real_escape_string($this->conn, $dg->getMaSv());
        $diem   = mysqli_real_escape_string($this->conn, $dg->getDiemRL());
        $nx     = mysqli_real_escape_string($this->conn, $dg->getNhanXet());
        $ngay   = mysqli_real_escape_string($this->conn, $dg->getNgay());
        $namhoc = mysqli_real_escape_string($this->conn, $dg->getNamHoc());
        $hocky  = mysqli_real_escape_string($this->conn, $dg->getHocKy());

       $sql = "INSERT INTO danhgiagvcn(ma_sv, diem_ren_luyen, nhan_xet, nam_hoc, hoc_ky, ngay)
        VALUES ('$ma_sv','$diem','$nx','$namhoc','$hocky','$ngay')";

        return mysqli_query($this->conn, $sql);
    }

    // CẬP NHẬT ĐÁNH GIÁ
    public function update(DanhGiaGVCN $dg)
    {
        $id     = mysqli_real_escape_string($this->conn, $dg->getId());
        $diem   = mysqli_real_escape_string($this->conn, $dg->getDiemRL());
        $nx     = mysqli_real_escape_string($this->conn, $dg->getNhanXet());
        $ngay   = mysqli_real_escape_string($this->conn, $dg->getNgay());
        $namhoc = mysqli_real_escape_string($this->conn, $dg->getNamHoc());
        $hocky  = mysqli_real_escape_string($this->conn, $dg->getHocKy());

        $sql = "UPDATE danhgiagvcn 
                SET diem_ren_luyen='$diem', nhan_xet='$nx', ngay='$ngay', 
                    nam_hoc='$namhoc', hoc_ky='$hocky'
                WHERE id='$id'";

        return mysqli_query($this->conn, $sql);
    }

    // XÓA ĐÁNH GIÁ
    public function delete($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $sql = "DELETE FROM danhgiagvcn WHERE id='$id'";
        return mysqli_query($this->conn, $sql);
    }

    // Lấy thông tin sinh viên theo mã
public function getSinhVien($ma_sv)
{
    $sql = "SELECT sv.*, l.ten_lop 
            FROM sinhvien sv
            LEFT JOIN lop l ON sv.ma_lop = l.ma_lop
            WHERE sv.ma_sv = ?";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $ma_sv);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();  // trả về 1 sinh viên
}


// Lấy bản đánh giá gần nhất của sinh viên
public function getLatestByMaSv($ma_sv)
{
    $sql = "SELECT *
            FROM danhgiagvcn
            WHERE ma_sv = ?
            ORDER BY nam_hoc DESC, hoc_ky DESC
            LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $ma_sv);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if(!$row) return null;

    // Trả về OBJECT
    return new DanhGiaGVCN(
        $row['id'],
        $row['ma_sv'],
        $row['diem_ren_luyen'],
        $row['nhan_xet'],
        $row['nam_hoc'],
        $row['hoc_ky'],
        $row['ngay'],
        $row['gv_chu_nhiem']
    );
}


public function resetDanhGia($id)
{
    $sql = "UPDATE danhgiagvcn 
            SET diem_ren_luyen=NULL, nhan_xet=NULL, ngay=NULL 
            WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

public function getAllLop() {
    $sql = "SELECT ma_lop, ten_lop FROM lop ORDER BY ten_lop ASC";
    return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

public function getAllNamHoc() {
    $sql = "SELECT DISTINCT nam_hoc FROM danhgiagvcn ORDER BY nam_hoc DESC";
    return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}


public function getAllHocKy() {
    $sql = "SELECT DISTINCT hoc_ky FROM danhgiagvcn ORDER BY hoc_ky ASC";
    return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}


public function getDanhSachSinhVien($lop, $nam_hoc, $hoc_ky, $search) {

    $sql = "
        SELECT sv.ma_sv, sv.ho_ten, sv.ma_lop, l.ten_lop,
               dg.id, dg.diem_ren_luyen, dg.nhan_xet, dg.ngay,
               dg.nam_hoc, dg.hoc_ky
        FROM sinhvien sv
        LEFT JOIN lop l ON sv.ma_lop = l.ma_lop
        LEFT JOIN danhgiagvcn dg ON sv.ma_sv = dg.ma_sv
        WHERE 1=1
    ";

    // Lọc lớp
    if ($lop != "") {
        $lop = $this->conn->real_escape_string($lop);
        $sql .= " AND sv.ma_lop='$lop' ";
    }

    // Lọc năm học
    if ($nam_hoc != "") {
        $nam_hoc = $this->conn->real_escape_string($nam_hoc);
        $sql .= " AND dg.nam_hoc='$nam_hoc' ";
    }

    // Lọc học kỳ
    if ($hoc_ky != "") {
        $hoc_ky = $this->conn->real_escape_string($hoc_ky);
        $sql .= " AND dg.hoc_ky='$hoc_ky' ";
    }

    // Tìm kiếm theo tên SV
    if ($search != "") {
        $search = $this->conn->real_escape_string($search);
        $sql .= " AND sv.ho_ten LIKE '%$search%' ";
    }

    $sql .= " ORDER BY sv.ma_lop, sv.ho_ten";

    return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}





}