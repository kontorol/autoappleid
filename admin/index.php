<?php
include("header.php");
if (isset($_GET['logout'])) {
    logout();
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}
?>
<head>
    <title>Bảng điều khiển quản trị viên</title>
</head>
<body>
<div class="container" style="margin-top: 1%">
    <div class="card border-dark">
        <h3 class="card-header">Thông tin máy chủ</h3>
        <ul class="list-group">
            <li class="list-group-item">
                <b>Tổng số tài khoản:</b> <?php echo $conn->query("SELECT id FROM account;")->rowCount(); ?>
            </li>
            <li class="list-group-item">
                <b>Tổng số người dùng:</b> <?php echo $conn->query("SELECT id FROM user;")->rowCount(); ?>
            </li>
            <li class="list-group-item">
                <b>Phiên bản PHP:</b><?php echo phpversion() ?>
                <?php if (ini_get('safe_mode')) {
                    echo 'An toàn';
                } else {
                    echo 'Không an toàn';
                } ?>
            </li>
            <li class="list-group-item">
                <b>Phiên bản MySQL:</b> <?php echo $conn->query("SELECT version()")->fetch()[0] ?>
            </li>
            <li class="list-group-item">
                <b>Máy chủ web:</b> <?php echo $_SERVER['SERVER_SOFTWARE'] ?>
            </li>
            <li class="list-group-item">
                <b>Hệ điều hành máy chủ:</b><?php echo php_uname('a') ?>
            </li>
            <li class="list-group-item">
                <b>Thời gian chạy tối đa:</b> <?php echo ini_get('max_execution_time') ?>s
            </li>
            <li class="list-group-item">
                <b>Giới hạn kích thước POST:</b> <?php echo ini_get('post_max_size'); ?>
            </li>
            <li class="list-group-item">
                <b>Giới hạn kích thước tệp được tải lên:</b> <?php echo ini_get('upload_max_filesize'); ?>
            </li>
        </ul>
    </div>
</div>
</body>
