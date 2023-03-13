<?php
include("../include/common.php");
if (!isset($_SESSION['isLogin']) or !isset($_SESSION["user_id"]) or !isadmin($_SESSION["user_id"])) {
    echo "<script>window.location.href='../userindex.php';</script>";
    exit;
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Bảng điều khiển quản trị viên</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="user.php">Danh sách người dùng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="account.php">Quản lý tài khoản</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shares.php">Quản lý trang chia sẻ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../userindex.php">Trở về trang cá nhân</a>
                </li>
            </ul>
            <?php if ($_SESSION['isLogin']) echo '<a href="../userindex.php?logout" class="btn btn-danger">Đăng xuất</a>' ?>
        </div>
    </div>
</nav>
