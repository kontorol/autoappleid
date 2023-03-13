<?php
include "include/common.php";
if (!isset($_GET['link'])) {
    echo "Liên kết chia sẻ không tồn tại";
    exit;
}
$account = new account(get_share_account_id($_GET['link']));
if ($account->id == -1) {
    echo "Liên kết chia sẻ không tồn tại";
    exit;
}
?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Chia sẻ tài khoản</title>
</head>
<body>
<script>
    var clipboard = new ClipboardJS('.btn');

    function alert_success() {
        Swal.fire({
            icon: 'success',
            title: 'Thông báo',
            text: 'Sao chép thành công',
            timer: 1000,
            timerProgressBar: true
        });
    }
</script>
<div class="container"
     style="align-self: center; position: absolute;width: <?php echo((isMobile()) ? "auto" : "20%"); ?>; margin-top:1rem">
    <div class="card border border-3 border-info shadow-lg" style="width: 20rem;">
        <div class="card-body">
            <h5 class="card-title">Thông tin tài khoản</h5>
            <h6 class="card-text"><?php echo $account->username ?></h6>
            <?php
            if ($account->frontend_remark != "") {
                echo "<p class='card-subtitle mb-2 text-muted'>Ghi chú: $account->frontend_remark</p>";
            }
            ?>
            <p class="card-subtitle mb-2 text-muted">Thời gian kiểm tra lần cuối: <?php echo $account->last_check ?></p>
            <p class="card-subtitle mb-2 text-muted">
                Trạng thái: <?php echo ($account->message == "Đang hoạt động" && ((time() - strtotime($account->last_check)) < (($account->check_interval + 2) * 60))) ? "<img src='resources/img/icons8-checkmark.svg' width='30' height='30'><span style='color: #549A31'>Đang hoạt động</span>" : "<img src='resources/img/icons8-cancel.svg' width='30' height='30'><span style='color: #B40404'>Không hoạt động</span>" ?></p>
            <button id="username" class="btn btn-primary" data-clipboard-text="<?php echo $account->username ?>"
                    onclick='alert_success()'>Sao chép tên đăng nhập
            </button>
            <button id="password" class="btn btn-success" data-clipboard-text="<?php echo $account->password ?>"
                    onclick='alert_success()'>Sao chép mật khẩu
            </button>
        </div>
    </div>
</div>
</body>
</html>