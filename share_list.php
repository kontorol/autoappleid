<?php
include("header.php");
?>
<title>Quản lý trang chia sẻ</title>
<div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
        <div class="table-responsive">
            <a href='share_edit.php?action=add' class='btn btn-secondary'>Thêm trang chia sẻ</a>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID trang</th>
                    <th>Ghi chú</th>
                    <th>Số lượng tài khoản</th>
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
                $result = $conn->prepare("SELECT id, share_link, account_list, remark FROM share WHERE owner = :owner;");
                $result->execute(['owner' => $_SESSION['user_id']]);
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch()) {
                        $account_list = explode(',', $row['account_list']);
                        $account_count = count($account_list);
                        $share_link = "{$Sys_config['apiurl']}/share_accounts.php?link={$row['share_link']}";
                        echo "<tr><td>{$row['id']}</td><td>{$row['remark']}</td><td>$account_count</td><td> <button id='share_link' class='btn btn-success ' data-clipboard-text='$share_link' onclick='alert_success()'>Sao chép liên kết</button> <a href='share_edit.php?action=edit&id={$row['id']}' class='btn btn-secondary'>Chỉnh sửa</a> <a href='share_edit.php?action=delete&id={$row['id']}' class='btn btn-danger'>Xóa</a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Không có trang chia sẻ</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
