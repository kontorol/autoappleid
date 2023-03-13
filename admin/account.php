<?php
include("header.php");
$currentuser = new user($_SESSION['user_id']);
?>
<title>Quản lý tài khoản</title>
<div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tài khoản</th>
                    <th>Ghi chú</th>
                    <th>Ghi chú phía trước</th>
                    <th>Trạng thái</th>
                    <th>Chủ sở hữu</th>
                    <th>Lần kiểm tra trước đó</th>
                    <th>Khoảng thời gian kiểm tra</th>
                    <th>Hoạt động</th>
                </tr>
                </thead>
                <?php
                global $conn;
                $stmt = $conn->query("SELECT * FROM account;");
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch()) {
                        $user_name = get_username_by_id($row['owner']);
                        echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['remark']}</td><td>{$row['frontend_remark']}</td><td>{$row['message']}</td><td>$user_name</td><td>{$row['last_check']}</td><td>{$row['check_interval']}</td><td><a href='account_edit.php?action=edit&id={$row['id']}' class='btn btn-secondary'>Sửa</a> <a href='account_edit.php?action=delete&id={$row['id']}' class='btn btn-danger'>Xóa</a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Không có tài khoản nào</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
