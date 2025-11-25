<?php
// Khởi động session để ghi nhớ thông tin đăng nhập
session_start();

// Nhúng file kết nối CSDL
include "DB/connect.php";

// KIỂM TRA NGƯỜI DÙNG ĐÃ ẤN NÚT "ĐĂNG NHẬP" CHƯA
if (isset($_POST["login"])) {

    // Lấy dữ liệu từ form: tên đăng nhập và mật khẩu
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Chống  SQL Injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // VALIDATE 
    // Kiểm tra các trường để trống
    if ($username == "" || $password == "") {
        echo "<script>alert('Tên đăng nhập và mật khẩu không được trống!');</script>";
        return;
    }

    // Kiểm tra độ dài
    if (strlen($username) < 3) {
        echo "<script>alert('Tên đăng nhập phải có ít nhất 3 ký tự!');</script>";
        return;
    }

    if (strlen($password) < 3) {
        echo "<script>alert('Mật khẩu phải có ít nhất 3 ký tự!');</script>";
        return;
    }

    // Kiểm tra ký tự hợp lệ -> username chỉ chữ + số + _
    if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        echo "<script>alert('Tên đăng nhập chỉ được chứa chữ, số và dấu gạch dưới (_)!');</script>";
        return;
    }

    // Câu truy vấn tìm tài khoản theo username và password
    $sql = "SELECT * FROM nguoidung WHERE username = '$username'";

    // Thực thi truy vấn
    $result = mysqli_query($conn, $sql);


    // Nếu có ít nhất 1 tài khoản tồn tại
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Kiểm tra mật khẩu đã mã hóa
        if (password_verify($password, $row['password'])) {

            // Lưu thông tin cần thiết vào session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['ma_khoa'] = $row['ma_khoa'];

            // PHÂN QUYỀN -> CHUYỂN TRANG TƯƠNG ỨNG
            switch ($row['role']) {
                case 'NV_Khoa':
                    header("Location: ./NVKhoa/nv_khoa.php");
                    break;
                case 'GV_CN':
                    header("location: ./GVCN/gv_cn.php");
                    break;
                case 'LD_Khoa':
                    header("Location: ./LDKhoa/ld_khoa.php");
                    break;
                case 'LD_Truong':
                    header("Location: ./LDTruong/ld_truong.php");
                    break;
            }
            exit;
        } else {
            // Sai mật khẩu -> thông báo lỗi
            echo "<script>alert('Sai mật khẩu!');</script>";
        }
    } else {
        // Không tìm thấy tài khoản trong hệ thống
        echo "<script>alert('Tài khoản không tồn tại!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang đăng nhập</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #eef3f9, #eef3f9);
        }

        /* Thẻ chứa form */
        .login-container {
            background: #fff;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 380px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tiêu đề */
        .login-container h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a73e8;
            margin-bottom: 25px;
        }

        /* Ô nhập */
        .input-group {
            width: 100%;
            text-align: left;
            margin-bottom: 20px;
        }

        .input-group label {
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #d0d0d0;
            border-radius: 10px;
            margin-top: 6px;
            font-size: 15px;
            color: #333;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 8px rgba(26, 115, 232, 0.3);
            outline: none;
        }

        /* Nút đăng nhập */
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a73e8, #4a90e2);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #1669c1, #2c74d4);
            transform: translateY(-2px);
        }

        /* Dòng footer nhỏ */
        .footer {
            margin-top: 18px;
            font-size: 13px;
            color: #666;
        }

        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>


<body>
    <form method="POST" class="login-container">
        <!-- Tên giao diện -->
        <h2>Đăng nhập hệ thống</h2>

        <div class="input-group">
            <!-- Tên đăng nhập -->
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
        </div>

        <div class="input-group">
            <!-- Mật khẩu -->
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <!-- Nút Đăng nhập -->
        <button type="submit" name="login">Đăng nhập</button>

        <div class="footer">
            <p>Quên mật khẩu? <a href="#">Khôi phục</a></p>
        </div>
    </form>
</body>

</html>