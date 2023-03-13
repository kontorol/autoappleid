<?php
include("header.php");
// Nếu đã đăng nhập thì chuyển đến trang người dùng
if (isset($_SESSION["isLogin"]) && $_SESSION["isLogin"]) {
    alert("error", "Bạn đã đăng nhập!", 1000, "userindex.php");
    exit;
}

if (isset($_POST['login'])) {
    if ($_POST["username"] == "" || $_POST["password"] == "") {
        alert("error", "Tên đăng nhập hoặc mật khẩu không được để trống!", 2000, "index.php#login");
        exit;
    } else {
        $result = login($_POST["username"], $_POST["password"]);
        if ($result[0]) {
            $_SESSION['isLogin'] = true;
            $_SESSION['user_id'] = get_id_by_username($_POST["username"]);
            echo "<script>window.location.href='userindex.php';</script>";
        } else {
            alert("error", $result[1], 2000, "index.php#login");
        }
    }
}
