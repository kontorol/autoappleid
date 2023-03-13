<?php
include("header.php");
?>
<title>Quản lý trang chia sẻ</title>
<div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID trang</th>
                    <th>Ghi chú</th>
                    <th>Số tài khoản</th>
                    <th>ID người dùng sở hữu</th>
                    <th>Đuôi liên kết</th>
                    <th>Hoạt động</th>
                </tr>
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
                </thead>
                <?php
                global $conn;
                $stmt = $conn->query("SELECT * FROM share;");
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch()){
                        $account_list = explode(',', $row['account_list']);
                        $account_count = count($account_list);
                        $share_link = "{$Sys_config['apiurl']}/share_accounts.php?link={$row['share_link']}";
                        echo "<tr><td>{$row['id']}</td><td>{$row['remark']}</td><td>$account_count</td><td>{$row['owner']}</td><td>{$row['share_link']}</td><td><button id='share_link' class='btn btn-success' data-clipboard-text='$share_link' onclick='alert_success()'>Sao chép liên kết</button><a href='share_edit.php?action=edit&id={$row['id']}' class='btn btn-secondary'>Chỉnh sửa</a><a href='share_edit.php?action=delete&id={$row['id']}' class='btn btn-danger'>Xóa</a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Không có trang chia sẻ nào</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
