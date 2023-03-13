<?php
include("header.php");
if (isset($_GET['logout'])) {
    logout();
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
$current_user = new user($_SESSION['user_id']);
?>
<title>Trung tâm người dùng</title>
<div class="container" style="margin-top: 1%">
    <div class="card border-dark">
        <h4 class="card-header">Trung tâm người dùng</h4>
        <ul class="list-group">
            <li class="list-group-item">
                <b>ID người dùng:</b> <?php echo $current_user->user_id ?>
            </li>
            <li class="list-group-item">
                <b>Số lượng tài khoản:</b>
                <?php
                global $conn;
                $account_list_result = $conn->prepare("SELECT COUNT(id) FROM account WHERE owner=:owner;");
                $account_list_result->execute(['owner' => $current_user->user_id]);
                echo $account_list_result->fetch()[0];
                ?>
            </li>
            <li class="list-group-item">
                <b>Số lượng trang chia sẻ:</b>
                <?php
                global $conn;
                $share_list_result = $conn->prepare("SELECT COUNT(id) FROM share WHERE owner=:owner;");
                $share_list_result->execute(['owner' => $current_user->user_id]);
                echo $share_list_result->fetch()[0];
                ?>
            </li>
        </ul>
    </div>
</div>
