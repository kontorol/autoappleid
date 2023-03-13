<?php
include "include/common.php";
?>
<!DOCTYPE HTML>
<!--
    Dimension by HTML5 UP
    html5up.net | @ajlkn
    Miễn phí cho sử dụng cá nhân và thương mại theo giấy phép CCA 3.0 (html5up.net/license)
-->
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="stylesheet" href="/resources/css/main.css"/>
    <noscript>
        <link rel="stylesheet" href="/resources/css/noscript.css"/>
    </noscript>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <title>Quản lý Apple ID</title>
</head>

<body>

<div id="wrapper">
    <header id="header">
        <div class="logo">
            <span class="icon fa-clipboard-check"></span>
        </div>
        <div class="content">
            <div class="inner">
                <h1>Quản lý Apple ID tự động</h1>
                <p>Cách mới để quản lý Apple ID</p>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#intro">Giới thiệu</a></li>
                <li><a href="#login">Đăng nhập</a></li>
                <?php if ($Sys_config["enable_register"]) echo "<li><a href='#register'>Đăng ký</a></li>" ?>
            </ul>
        </nav>
    </header>
    <div id="main">
        <article id="intro">
            <h2 class="major">Giới thiệu</h2>
            <p>Dựa trên câu hỏi bảo mật, tự động mở khóa Apple ID, tự động tắt xác thực hai yếu tố, cung cấp hiển thị tài khoản phía trước, hỗ trợ nhiều tài khoản</p>
        </article>
        <article id="login">
            <?php
            if (isset($_SESSION['isLogin']) and $_SESSION['isLogin']) {
                alert("warning", "Bạn đã đăng nhập, tự động chuyển đến giao diện người dùng!", 1000, "userindex.php");
                exit;
            }
            ?>
            <h2 class="major">Đăng nhập</h2>
            <form action="login.php" method="post">
                <div class="field half first">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập"/>
                </div>
                <div class="field half">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu"/>
                </div>
                <ul class="actions">
                    <li><input type="submit" value="Đăng nhập" class="primary special" name="login"/></li>
                </ul>
            </form>
        </article>
        <article id="register">
            <?php
            if (isset($_SESSION['isLogin']) and $_SESSION['isLogin']) {
                alert("warning", "Bạn đã đăng nhập, tự động chuyển đến giao diện người dùng!", 1000, "userindex.php");
                exit;
            }
            ?>
            <h2 class="major">Đăng ký</h2>
            <form action="register.php" method="post">
                <div class="field half first">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập"/>
                </div>
                <div class="field half">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu"/>
                </div>
                <ul class="actions">
                    <li><input type="submit" value="Đăng ký" class="primary special" name="register"/></li>
                </ul>
            </form>
        </article>
    </div>
    <?php include "footer.php"; ?>
</div>
<div id="bg"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
<script src="/resources/js/util.js"></script>
<script src="/resources/js/main.js"></script>
<script>
    $(function () {
        $(window).load(function () {
            NProgress.done();
        });
        NProgress.set(0.0);
        NProgress.configure({showSpinner: false});
        NProgress.configure({minimum: 0.4});
        NProgress.configure({easing: 'ease', speed: 1200});
        NProgress.configure({trickleSpeed: 200});
        NProgress.configure({trickleRate: 0.2, trickleSpeed: 1200});
        NProgress.inc();
        $(window).ready(function () {
            NProgress.start();
        });
    });
</script>
</body>
</html>
