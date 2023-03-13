<?php
include("include/common.php");
if (!isset($_SESSION['isLogin'])) {
    $_SESSION['isLogin'] = false;
}
if ((!$_SESSION['isLogin']) and (!in_array(php_self(), array("index.php", "login.php", "register.php")))) {
    echo "<script>window.location.href='index.php#login';</script>"; // Chuyển hướng đến trang đăng nhập
    exit;
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="userindex.php">Quản lý tự động AppleID</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                if ($_SESSION['isLogin']) {
                    echo "<li class='nav-item'>
                    <a class='nav-link' href='userindex.php'>Trung tâm người dùng</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='account.php'>Quản lý tài khoản</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='share_list.php'>Quản lý trang chia sẻ</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='proxy.php'>Danh sách đại diện</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='user_info.php'>Thông tin cá nhân</a>
                </li>";
                } else {
                    echo "<li class='nav-item'>
                    <a class='nav-link' href='index.php'>Trang chủ</a>
                </li>";
                } ?>

                <?php if ((isset($_SESSION['user_id'])) and (isadmin($_SESSION['user_id']))) {
                    echo "
                <li class='nav-item'>
                    <a class='nav-link' href='/admin'>Bảng điều khiển</a>
                </li>";
                } ?>
            </ul>
            <?php if ($_SESSION['isLogin']) {
                echo '<a href="userindex.php?logout" class="btn btn-danger">Đăng xuất</a>';
            } else {
                echo '<a href="index.php#login" class="btn btn-success">Đăng nhập</a>';
            } ?>
        </div>
    </div>
</nav>
