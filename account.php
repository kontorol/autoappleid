<?php
include("header.php");
$currentuser = new user($_SESSION['user_id']);
?>
<title>Quản lý tài khoản</title>
<div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
        <div class="table-responsive">
            <a href='account_edit.php?action=add' class='btn btn-secondary'>Thêm tài khoản</a>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tài khoản</th>
                    <th>Mật khẩu</th>
                    <th>Ghi chú</th>
                    <th>Ghi chú phía trước</th>
                    <th>Trạng thái</th>
                    <th>Lần kiểm tra cuối cùng</th>
                    <th>Khoảng thời gian kiểm tra</th>
                    <th>Tùy chọn thao tác</th>
                </tr>
                <script>
                    var clipboard = new ClipboardJS('.btn');

                    function alert_success() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thông báo',
                            text: 'Đã sao chép',
                            timer: 1000,
                            timerProgressBar: true
                        });
                    }
                </script>
                </thead>
                <?php
                global $conn;
                $stmt = $conn->prepare("SELECT * FROM account WHERE owner = :userid;");
                $stmt->execute(['userid' => $currentuser->user_id]);
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch()) {
                        $share_link = "{$Sys_config['apiurl']}/share.php?link={$row['share_link']}";
                        echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['password']}</td><td>{$row['remark']}</td><td>{$row['frontend_remark']}</td><td>{$row['message']}</td><td>{$row['last_check']}</td><td>{$row['check_interval']}</td><td> <button id='share_link' class='btn btn-success' data-clipboard-text='$share_link' onclick='alert_success()'>Sao chép liên kết</button> <a href='account_edit.php?action=edit&id={$row['id']}' class='btn btn-secondary'>Chỉnh sửa</a> <a href='account_edit.php?action=delete&id={$row['id']}' class='btn btn-danger'>Xóa</a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Không có tài khoản</td></tr>";
                }
                ?>
            </table>

        </div>
    </div>
</div>
