<?php
include("header.php");
// Nếu đã đăng nhập thì chuyển hướng đến trang người dùng
if (isset($_SESSION["isLogin"]) && $_SESSION["isLogin"]) {
    alert("error","Bạn đã đăng nhập!",1000,"userindex.php");
    exit;
}
// Nếu như chế độ đăng ký đang bật và người dùng đã gửi yêu cầu đăng ký
if (isset($_POST['register']) && $Sys_config['enable_register']) {
    // Kiểm tra xem người dùng đã nhập đủ thông tin đăng ký hay chưa
    if ($_POST["username"] == null || $_POST["password"] == null) {
        alert("error","Tên đăng nhập hoặc mật khẩu không được để trống!",2000,"index.php#register");
        exit;
    } else {
        // Thực hiện đăng ký và hiển thị thông báo kết quả
        $feed = register($_POST["username"], $_POST["password"]);
        if (!$feed[0]) {
            alert("error",$feed[1],2000,"index.php#register");
        } else {
            alert("success","Đăng ký thành công!",2000,"index.php#login");
        }

    }
}
?>
