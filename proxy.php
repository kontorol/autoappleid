<?php
include("header.php");
?>
<title>Quản lý Proxy</title>
<div class="container" style="padding-top:70px;">
    <div class="col-md-12 center-block" style="float: none;">
        <div class="table-responsive">
            <a href='proxy_edit.php?action=add' class='btn btn-secondary'>Thêm Proxy</a>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID Proxy</th>
                    <th>Giao thức</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Lần sử dụng cuối</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <?php
                global $conn;
                $result = $conn->prepare("SELECT * FROM proxy WHERE owner = :owner;");
                $result->execute(['owner' => $_SESSION['user_id']]);
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch()) {
                        $status = $row['status'] ? "Đang sử dụng" : "Không sử dụng";
                        echo "<tr><td>{$row['id']}</td><td>{$row['protocol']}</td><td>{$row['content']}</td><td>$status</td><td>{$row['last_use']}</td><td> <a href='proxy_edit.php?action=edit&id={$row['id']}' class='btn btn-secondary'>Sửa</a> <a href='proxy_edit.php?action=delete&id={$row['id']}' class='btn btn-danger'>Xóa</a></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Chưa thêm Proxy</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
