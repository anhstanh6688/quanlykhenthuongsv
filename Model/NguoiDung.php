<?php
class NguoiDung
{
    // Các thuộc tính của người dùng
    public $user_id;
    public $username;
    public $password;
    public $role;
    public $ma_khoa;

    // HÀM KHỞI TẠO 
    public function __construct($row)
    {
        $this->user_id  = $row['user_id'];
        $this->username = $row['username'];
        $this->password = $row['password'];
        $this->role     = $row['role'];
        $this->ma_khoa  = $row['ma_khoa'];
    }
}
