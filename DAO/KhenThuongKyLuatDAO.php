<?php
class KhenThuongKyLuatDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db; // $db là mysqli object
    }

    // Lấy tất cả
    public function getAll()
    {
        $sql = "SELECT * FROM khenthuongkyluatnhanvien ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy theo ID
    public function getById($id)
    {
        $sql = "SELECT * FROM khenthuongkyluatnhanvien WHERE id=? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    // Thêm mới
    public function insert($data)
    {
        $sql = "INSERT INTO khenthuongkyluatnhanvien (ma_nv, loai, cap_quan_ly, noi_dung, ngay, nguoi_duyet) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $data['ma_nv'],
            $data['loai'],
            $data['cap_quan_ly'],
            $data['noi_dung'],
            $data['ngay'],
            $data['nguoi_duyet']
        );
        if (!$stmt->execute()) {
            echo "Execute error: " . $stmt->error;
            return false;
        }
        return true;
        // return $stmt->execute();
    }

    // Cập nhật
    public function update($id, $data)
    {
        $sql = "UPDATE khenthuongkyluatnhanvien SET ma_nv=?, loai=?, cap_quan_ly=?, noi_dung=?, ngay=?, nguoi_duyet=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $data['ma_nv'],
            $data['loai'],
            $data['cap_quan_ly'],
            $data['noi_dung'],
            $data['ngay'],
            $data['nguoi_duyet'],
            $id
        );
        return $stmt->execute();
    }

    // Xóa
    public function delete($id)
    {
        $sql = "DELETE FROM khenthuongkyluatnhanvien WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
